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
        $month = (int) $request->get('month', now()->format('m'));
        $year = (int) $request->get('year', now()->format('Y'));
        $employeeId = $request->get('employee_id');

        $startOfMonth = Carbon::create($year, $month, 1)->startOfDay();
        $endOfMonth = Carbon::create($year, $month, 1)->endOfMonth()->endOfDay();

        // holidays in the month
        $holidays = Holiday::whereBetween('date', [$startOfMonth->toDateString(), $endOfMonth->toDateString()])
            ->pluck('date')
            ->map(fn($d) => Carbon::parse($d)->toDateString())
            ->toArray();

        // officeDays computed excluding weekends + holidays (change if you want otherwise)
        $officeDays = $this->computeOfficeDays($startOfMonth, $endOfMonth, $holidays, excludeWeekends: true);

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
            'month' => str_pad($month, 2, '0', STR_PAD_LEFT),
            'year' => $year,
            'officeDays' => $officeDays,
            'selectedEmployee' => $employeeId
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

        $officeDays = $this->computeOfficeDays($startOfMonth, $endOfMonth, $holidays, true);

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
        $month = (int)$request->get('month', now()->format('m'));
        $year = (int)$request->get('year', now()->format('Y'));

        return Excel::download(new SalaryReportExport($month, $year), 'salary_report_'.$year.'_'.$month.'.xlsx');
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
