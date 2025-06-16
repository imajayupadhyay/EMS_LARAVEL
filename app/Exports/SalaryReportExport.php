<?php

namespace App\Exports;

use App\Models\Employee;
use App\Models\Punch;
use App\Models\LeaveApplication;
use App\Models\LeaveAssignment;
use App\Models\Holiday;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class SalaryReportExport implements FromView
{
    protected $month;
    protected $year;

    public function __construct($month, $year)
    {
        $this->month = $month;
        $this->year = $year;
    }

    public function view(): View
    {
        $holidays = Holiday::whereYear('date', $this->year)
            ->whereMonth('date', $this->month)
            ->pluck('date')
            ->toArray();

        $daysInMonth = Carbon::create($this->year, $this->month)->daysInMonth;
        $officeDays = collect(range(1, $daysInMonth))->reduce(function ($carry, $day) use ($holidays) {
            $date = Carbon::create($this->year, $this->month, $day);
            if (!$date->isWeekend() && !in_array($date->toDateString(), $holidays)) {
                return $carry + 1;
            }
            return $carry;
        }, 0);

        $employees = Employee::with(['department', 'designation'])->get();

        $report = $employees->map(function ($employee) {
            $presentDays = Punch::where('employee_id', $employee->id)
                ->whereMonth('punched_in_at', $this->month)
                ->whereYear('punched_in_at', $this->year)
                ->selectRaw('DATE(punched_in_at) as day')
                ->groupBy('day')
                ->get()
                ->count();

            $approvedLeaves = LeaveApplication::where('employee_id', $employee->id)
                ->where('status', 'approved')
                ->where(function ($q) {
                    $q->whereMonth('start_date', $this->month)
                      ->whereYear('start_date', $this->year);
                })
                ->get();

            $approvedLeaveDays = 0;
            foreach ($approvedLeaves as $leave) {
                $approvedLeaveDays += Carbon::parse($leave->start_date)->diffInDays(Carbon::parse($leave->end_date)) + 1;
            }

            $leaveAssignments = LeaveAssignment::where('employee_id', $employee->id)->get();
            $totalAssigned = $leaveAssignments->sum('total_assigned');
            $balance = $leaveAssignments->sum('balance');

            return [
                'employee' => $employee,
                'present_days' => $presentDays,
                'approved_leaves' => $approvedLeaveDays,
                'leave_assigned' => $totalAssigned,
                'leave_balance' => $balance
            ];
        });

        return view('exports.salary_report', [
            'report' => $report,
            'officeDays' => $officeDays,
            'month' => $this->month,
            'year' => $this->year
        ]);
    }
}
