<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Punch;
use App\Models\Holiday;
use App\Models\LeaveApplication;
use App\Models\LeaveAssignment;
use Carbon\Carbon;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SalaryReportExport;

class SalaryReportController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->get('month', now()->format('m'));
        $year = $request->get('year', now()->format('Y'));
        $employeeId = $request->get('employee_id');

        $holidays = Holiday::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->pluck('date')
            ->toArray();

        $daysInMonth = Carbon::create($year, $month)->daysInMonth;
        $officeDays = $daysInMonth - count($holidays);

        $employees = Employee::with(['department', 'designation'])->get();

        $report = $employees
            ->when($employeeId, fn ($q) => $q->where('id', $employeeId))
            ->map(function ($employee) use ($month, $year) {
                $presentDays = Punch::where('employee_id', $employee->id)
                    ->whereMonth('punched_in_at', $month)
                    ->whereYear('punched_in_at', $year)
                    ->selectRaw('DATE(punched_in_at) as day')
                    ->groupBy('day')
                    ->get()
                    ->count();

                $approvedLeaves = LeaveApplication::where('employee_id', $employee->id)
                    ->where('status', 'approved')
                    ->whereMonth('start_date', $month)
                    ->whereYear('start_date', $year)
                    ->get();

                $approvedLeaveDays = $approvedLeaves->reduce(function ($carry, $leave) {
                    return $carry + (Carbon::parse($leave->start_date)->diffInDays(Carbon::parse($leave->end_date)) + 1);
                }, 0);

                $assignments = LeaveAssignment::where('employee_id', $employee->id)->get();
                $totalAssigned = $assignments->sum('total_assigned');
                $balance = $assignments->sum('balance');

                return [
                    'employee' => $employee,
                    'present_days' => $presentDays,
                    'approved_leaves' => $approvedLeaveDays,
                    'leave_assigned' => $totalAssigned,
                    'leave_balance' => $balance
                ];
            });

        return Inertia::render('Admin/SalaryReport/Index', [
            'report' => $report,
            'employees' => $employees->map(fn ($e) => [
                'id' => $e->id,
                'name' => $e->first_name . ' ' . $e->last_name
            ]),
            'month' => $month,
            'year' => $year,
            'officeDays' => $officeDays,
            'selectedEmployee' => $employeeId
        ]);
    }

    public function export(Request $request)
    {
        return Excel::download(new SalaryReportExport($request->month, $request->year), 'salary_report.xlsx');
    }
}
