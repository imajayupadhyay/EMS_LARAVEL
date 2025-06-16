<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\User;
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

        $holidays = Holiday::whereYear('date', $year)
            ->whereMonth('date', $month)
            ->pluck('date')
            ->toArray();

        $daysInMonth = Carbon::create($year, $month)->daysInMonth;
        $officeDays = collect(range(1, $daysInMonth))->reduce(function ($carry, $day) use ($month, $year, $holidays) {
            $date = Carbon::create($year, $month, $day);
            if (!$date->isWeekend() && !in_array($date->toDateString(), $holidays)) {
                return $carry + 1;
            }
            return $carry;
        }, 0);

        $employees = Employee::with(['department', 'designation'])->get();

        $report = $employees->map(function ($employee) use ($month, $year) {
            $user = User::where('email', $employee->email)->first();

            if (!$user) {
                return [
                    'employee' => $employee,
                    'present_days' => 0,
                    'approved_leaves' => 0,
                    'leave_assigned' => 0,
                    'leave_balance' => 0
                ];
            }

            $presentDays = Punch::where('user_id', $user->id)
                ->whereMonth('punched_in_at', $month)
                ->whereYear('punched_in_at', $year)
                ->selectRaw('DATE(punched_in_at) as day')
                ->groupBy('day')
                ->get()
                ->count();

            $approvedLeaves = LeaveApplication::where('user_id', $user->id)
                ->where('status', 'approved')
                ->where(function ($q) use ($month, $year) {
                    $q->whereMonth('start_date', $month)
                      ->whereYear('start_date', $year);
                })
                ->get();

            $approvedLeaveDays = 0;
            foreach ($approvedLeaves as $leave) {
                $approvedLeaveDays += Carbon::parse($leave->start_date)->diffInDays(Carbon::parse($leave->end_date)) + 1;
            }

            $leaveAssignments = LeaveAssignment::where('user_id', $user->id)->get();
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

        return Inertia::render('Admin/SalaryReport/Index', [
            'report' => $report,
            'month' => $month,
            'year' => $year,
            'officeDays' => $officeDays
        ]);
    }

    public function export(Request $request)
    {
        return Excel::download(new SalaryReportExport($request->month, $request->year), 'salary_report.xlsx');
    }
}

