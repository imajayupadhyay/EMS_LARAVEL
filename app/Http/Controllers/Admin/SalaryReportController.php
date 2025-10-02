<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Punch;
use App\Models\Holiday;
use App\Models\LeaveApplication;
use App\Models\LeaveAssignment;
use App\Models\Salary;
use App\Models\SalaryItem;
use App\Models\PayrollPeriod;
use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalaryReportExport;

class SalaryReportController extends Controller
{
    /**
     * Index / preview the salary report for a month.
     */
    public function index(Request $request)
    {
        // Handle month in YYYY-MM format from frontend
        $monthInput = $request->get('month', now()->format('Y-m'));
        if (str_contains($monthInput, '-')) {
            [$year, $month] = explode('-', $monthInput);
            $year = (int) $year;
            $month = (int) $month;
        } else {
            $month = (int) $monthInput;
            $year = (int) $request->get('year', now()->format('Y'));
        }

        $employeeId = $request->get('employee_id');

        $startOfMonth = Carbon::create($year, $month, 1)->startOfDay();
        $endOfMonth = Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();

        // holidays in the month
        $holidays = Holiday::whereBetween('date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->pluck('date')
            ->map(fn($d) => Carbon::parse($d)->toDateString())
            ->toArray();

        // officeDays computed excluding only holidays (no weekend off - employees work all days)
        $officeDays = $this->computeOfficeDays($startOfMonth, $endOfMonth, $holidays, excludeWeekends: false);

        // employees filtered at DB level
        $employeesQuery = Employee::with(['department','designation'])
            ->when($employeeId, fn($q) => $q->where('id', $employeeId))
            ->orderBy('first_name');

        $employees = $employeesQuery->get();

        // build basic attendance/leave report (preload and aggregate)
        $baseReport = $this->buildReport($employees, $startOfMonth, $endOfMonth, $holidays);

        // compute complete salary rows (earnings/deductions/net)
        $salaryRows = $baseReport->map(fn($row) => $this->calculateSalaryForEmployee($row, $officeDays));

        return Inertia::render('Admin/SalaryReport/Index', [
            'report' => $salaryRows,
            'employees' => $employees->map(fn ($e) => ['id' => $e->id, 'name' => trim($e->first_name.' '.$e->last_name)]),
            'month' => sprintf('%04d-%02d', $year, $month),
            'year' => (string) $year,
            'officeDays' => $officeDays,
            'selectedEmployee' => $employeeId
        ]);
    }

    /**
     * Update salary data for an employee
     */
    public function update(Request $request)
    {
        $data = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'month' => 'required|string',
            'year' => 'required|string',
            'gross_salary' => 'required|numeric|min:0',
            'earnings' => 'required|array',
            'earnings.*.label' => 'required|string',
            'earnings.*.amount' => 'required|numeric',
            'deductions' => 'required|array',
            'deductions.*.label' => 'required|string',
            'deductions.*.amount' => 'required|numeric',
            'meta' => 'nullable|array'
        ]);

        // Parse month
        if (str_contains($data['month'], '-')) {
            [$year, $month] = explode('-', $data['month']);
        } else {
            $month = $data['month'];
            $year = $data['year'];
        }

        // Find or create payroll period
        $payrollPeriod = PayrollPeriod::firstOrCreate(
            ['year' => (int) $year, 'month' => (int) $month],
            [
                'start_date' => Carbon::create($year, $month, 1)->startOfMonth()->toDateString(),
                'end_date' => Carbon::create($year, $month, 1)->endOfMonth()->toDateString(),
                'status' => 'draft',
            ]
        );

        // Find or create salary record
        $salary = Salary::updateOrCreate(
            [
                'employee_id' => $data['employee_id'],
                'payroll_period_id' => $payrollPeriod->id
            ],
            [
                'gross_salary' => $data['gross_salary'],
                'total_earnings' => collect($data['earnings'])->sum('amount'),
                'total_deductions' => collect($data['deductions'])->sum('amount'),
                'net_salary' => collect($data['earnings'])->sum('amount') - collect($data['deductions'])->sum('amount'),
                'status' => 'finalized',
                'meta' => $data['meta'] ?? null,
                'created_by' => Auth::id(),
                'finalized_by' => Auth::id(),
                'finalized_at' => Carbon::now(),
            ]
        );

        // Delete existing salary items
        $salary->items()->delete();

        // Create new earnings
        foreach ($data['earnings'] as $earning) {
            if (!empty($earning['label']) && isset($earning['amount'])) {
                SalaryItem::create([
                    'salary_id' => $salary->id,
                    'type' => 'earning',
                    'code' => null,
                    'label' => $earning['label'],
                    'amount' => $earning['amount'],
                ]);
            }
        }

        // Create new deductions
        foreach ($data['deductions'] as $deduction) {
            if (!empty($deduction['label']) && isset($deduction['amount'])) {
                SalaryItem::create([
                    'salary_id' => $salary->id,
                    'type' => 'deduction',
                    'code' => null,
                    'label' => $deduction['label'],
                    'amount' => $deduction['amount'],
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Salary updated successfully'
        ]);
    }

    /**
     * Re-usable: build attendance + leave aggregates per employee collection
     */
    protected function buildReport($employees, Carbon $startOfMonth, Carbon $endOfMonth, array $holidays = [])
    {
        // Preload present-day counts
        $punches = Punch::select('employee_id', DB::raw('DATE(punched_in_at) as day'))
            ->whereBetween('punched_in_at', [$startOfMonth->toDateTimeString(), $endOfMonth->toDateTimeString()])
            ->groupBy('employee_id', 'day')
            ->get()
            ->groupBy('employee_id')
            ->map(fn($g) => $g->count());

        // Leaves overlapping month (approved)
        $leaves = LeaveApplication::where('status', 'approved')
            ->where(function ($q) use ($startOfMonth, $endOfMonth) {
                $q->whereDate('start_date', '<=', $endOfMonth->toDateString())
                  ->whereDate('end_date', '>=', $startOfMonth->toDateString());
            })
            ->get()
            ->groupBy('employee_id');

        // Leave assignments aggregates
        $assignments = LeaveAssignment::select('employee_id', DB::raw('SUM(total_assigned) as total_assigned'), DB::raw('SUM(balance) as balance'))
            ->groupBy('employee_id')
            ->get()
            ->keyBy('employee_id');

        return $employees->map(function ($employee) use ($punches, $leaves, $assignments, $startOfMonth, $endOfMonth, $holidays) {
            $empId = $employee->id;

            $presentDays = $punches->get($empId, 0);

            // compute approved leave days overlapping the month
            $approvedLeaveDays = 0;
            if (isset($leaves[$empId])) {
                foreach ($leaves[$empId] as $leave) {
                    $leaveStart = Carbon::parse($leave->start_date)->startOfDay();
                    $leaveEnd   = Carbon::parse($leave->end_date)->endOfDay();

                    $overlapStart = $leaveStart->greaterThan($startOfMonth) ? $leaveStart : $startOfMonth;
                    $overlapEnd   = $leaveEnd->lessThan($endOfMonth) ? $leaveEnd : $endOfMonth;

                    if ($overlapStart->lte($overlapEnd)) {
                        $approvedLeaveDays += $overlapStart->diffInDays($overlapEnd) + 1;
                    }
                }
            }

            $assignment = $assignments->get($empId);
            $totalAssigned = $assignment->total_assigned ?? 0;
            $balance = $assignment->balance ?? 0;

            return [
                'employee' => $employee,
                'present_days' => (int)$presentDays,
                'approved_leaves' => (int)$approvedLeaveDays,
                'leave_assigned' => (float)$totalAssigned,
                'leave_balance' => (float)$balance
            ];
        });
    }

    /**
     * Calculate salary breakdown for one employee row.
     *
     * This function is central: change business rules here.
     */
    protected function calculateSalaryForEmployee(array $row, int $officeDays): array
    {
        $employee = $row['employee'];
        $presentDays = (int)($row['present_days'] ?? 0);
        $approvedLeaves = (int)($row['approved_leaves'] ?? 0);
        $leaveBalance = (float)($row['leave_balance'] ?? 0);

        // monthly gross from employee model (must exist)
        $monthlySalary = (float)($employee->monthly_salary ?? 0);

        // paid leave days = min(approvedLeaves, floor(leaveBalance))
        $paidLeaveDays = min($approvedLeaves, (int) floor($leaveBalance));
        $unpaidLeaveDays = max(0, $approvedLeaves - $paidLeaveDays);

        // absent days = max(0, officeDays - presentDays - paidLeaveDays)
        $absentDays = max(0, $officeDays - $presentDays - $paidLeaveDays);

        // unpaid days that attract deduction = absentDays + unpaidLeaveDays
        $unpaidDeductDays = $absentDays + $unpaidLeaveDays;

        $perDay = $officeDays > 0 ? round($monthlySalary / $officeDays, 2) : 0.0;
        $unpaidLeaveDeduction = round($perDay * $unpaidDeductDays, 2);

        // Earnings: Basic (gross). Extendable later with components.
        $earnings = [
            'Basic (Gross)' => round($monthlySalary, 2),
        ];
        $totalEarnings = array_sum($earnings);

        // Deductions: unpaid + statutory percent (configurable)
        $deductions = [
            '₹' => $unpaidLeaveDeduction
        ];

        $statutoryPercent = config('hrms.statutory_deduction_percent', 0);
        if ($statutoryPercent > 0) {
            $statutory = round(($totalEarnings - $unpaidLeaveDeduction) * ($statutoryPercent / 100), 2);
            $deductions['Statutory (' . $statutoryPercent . '%)'] = $statutory;
        }

        $totalDeductions = array_sum($deductions);
        $netSalary = round($totalEarnings - $totalDeductions, 2);

        return [
            'employee' => $employee,
            'present_days' => $presentDays,
            'approved_leaves' => $approvedLeaves,
            'paid_leave_days' => $paidLeaveDays,
            'unpaid_leave_days' => $unpaidLeaveDays,
            'absent_days' => $absentDays,
            'working_days' => $officeDays,
            'gross_salary' => round($monthlySalary, 2),
            'earnings' => $earnings,
            'deductions' => $deductions,
            'total_earnings' => $totalEarnings,
            'total_deductions' => $totalDeductions,
            'net_salary' => $netSalary
        ];
    }

    /**
     * Finalize and persist salaries in bulk (or per-employee).
     *
     * Request payload: month, year, optional employee_id, overwrite (boolean)
     */
    public function finalize(Request $request)
    {
        $data = $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000',
            'employee_id' => 'nullable|exists:employees,id',
            'overwrite' => 'nullable|boolean'
        ]);

        $month = (int)$data['month'];
        $year = (int)$data['year'];
        $employeeId = $data['employee_id'] ?? null;
        $overwrite = (bool)($data['overwrite'] ?? false);

        $startOfMonth = Carbon::create($year, $month, 1)->startOfDay();
        $endOfMonth = Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();

        $holidays = Holiday::whereBetween('date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->pluck('date')->map(fn($d) => Carbon::parse($d)->toDateString())->toArray();

        $officeDays = $this->computeOfficeDays($startOfMonth, $endOfMonth, $holidays, false);

        $employees = Employee::when($employeeId, fn($q) => $q->where('id', $employeeId))->get();

        $baseReport = $this->buildReport($employees, $startOfMonth, $endOfMonth, $holidays);
        $salaryRows = $baseReport->map(fn($row) => $this->calculateSalaryForEmployee($row, $officeDays));

        // create or update Salary records
        $created = 0; $updated = 0; $skipped = 0;
        DB::beginTransaction();
        try {
            // create or attach payroll period
            $payrollPeriod = PayrollPeriod::firstOrCreate(
                ['year' => $year, 'month' => $month],
                ['start_date' => $startOfMonth->toDateString(), 'end_date' => $endOfMonth->toDateString(), 'status' => 'finalized', 'finalized_by' => Auth::id(), 'finalized_at' => Carbon::now()]
            );

            foreach ($salaryRows as $row) {
                $empId = $row['employee']->id;

                $salary = Salary::where('employee_id', $empId)->where('payroll_period_id', $payrollPeriod->id)->first();

                if ($salary && !$overwrite) {
                    $skipped++;
                    continue;
                }

                if ($salary && $overwrite) {
                    $salary->items()->delete();
                } else {
                    $salary = new Salary();
                }

                $salary->employee_id = $empId;
                $salary->payroll_period_id = $payrollPeriod->id;
                $salary->gross_salary = $row['gross_salary'];
                $salary->total_earnings = $row['total_earnings'];
                $salary->total_deductions = $row['total_deductions'];
                $salary->net_salary = $row['net_salary'];
                $salary->status = 'finalized';
                $salary->meta = [
                    'present_days' => $row['present_days'],
                    'approved_leaves' => $row['approved_leaves'],
                    'paid_leave_days' => $row['paid_leave_days'],
                    'unpaid_leave_days' => $row['unpaid_leave_days'],
                    'absent_days' => $row['absent_days'],
                    'working_days' => $officeDays
                ];
                $salary->created_by = Auth::id();
                $salary->finalized_by = Auth::id();
                $salary->finalized_at = Carbon::now();
                $salary->save();

                // save salary items
                foreach ($row['earnings'] as $label => $amount) {
                    SalaryItem::create([
                        'salary_id' => $salary->id,
                        'type' => 'earning',
                        'code' => null,
                        'label' => $label,
                        'amount' => $amount,
                    ]);
                }
                foreach ($row['deductions'] as $label => $amount) {
                    SalaryItem::create([
                        'salary_id' => $salary->id,
                        'type' => 'deduction',
                        'code' => null,
                        'label' => $label,
                        'amount' => $amount,
                    ]);
                }

                if ($overwrite && isset($exists)) $updated++; else $created++;
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Salary finalization error: '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return back()->with('error', 'Failed to finalize salaries: '.$e->getMessage());
        }

        return back()->with('success', "Salaries processed. Created: $created, Updated: $updated, Skipped: $skipped");
    }

    /**
     * Excel export — by default export finalized salaries for the payroll period.
     */
    public function export(Request $request)
    {
        try {
            // Handle month in YYYY-MM format from frontend
            $monthInput = $request->get('month', now()->format('Y-m'));
            if (str_contains($monthInput, '-')) {
                [$year, $month] = explode('-', $monthInput);
                $year = (int) $year;
                $month = (int) $month;
            } else {
                $month = (int) $monthInput;
                $year = (int) $request->get('year', now()->format('Y'));
            }

            $fileName = 'salary_report_' . $year . '_' . str_pad($month, 2, '0', STR_PAD_LEFT) . '.xlsx';

            return Excel::download(new SalaryReportExport($month, $year), $fileName);

        } catch (\Exception $e) {
            Log::error('Salary Export Download Error: ' . $e->getMessage());
            return back()->with('error', 'Failed to export salary report: ' . $e->getMessage());
        }
    }

    /**
     * Compute office days in given range (exclude weekends by default).
     */
    protected function computeOfficeDays(Carbon $start, Carbon $end, array $holidays = [], bool $excludeWeekends = true)
    {
        $days = 0;
        $cursor = $start->copy();
        while ($cursor->lte($end)) {
            $isWeekend = in_array($cursor->dayOfWeekIso, [6,7]); // 6 Sat, 7 Sun
            $isHoliday = in_array($cursor->toDateString(), $holidays);
            if ($excludeWeekends) {
                if (! $isWeekend && ! $isHoliday) $days++;
            } else {
                if (! $isHoliday) $days++;
            }
            $cursor->addDay();
        }
        return $days;
    }
}
