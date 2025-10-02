<?php

namespace App\Exports;

use App\Models\Punch;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class AttendanceExport implements FromView
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        // Determine date range
        $fromDate = $this->request->from_date;
        $toDate = $this->request->to_date;
        $month = $this->request->month;

        if ($fromDate && $toDate) {
            $startDate = Carbon::parse($fromDate);
            $endDate = Carbon::parse($toDate);
        } elseif ($month) {
            $startDate = Carbon::parse($month)->startOfMonth();
            $endDate = Carbon::parse($month)->endOfMonth();
        } else {
            // Default to current month
            $startDate = Carbon::now()->startOfMonth();
            $endDate = Carbon::now()->endOfMonth();
        }

        // Get all dates in range
        $dates = [];
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $dates[] = $currentDate->format('Y-m-d');
            $currentDate->addDay();
        }

        // Query punches for the date range
        $query = Punch::with(['employee.designation', 'employee.department'])
            ->whereBetween('punched_in_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->when($this->request->employee_id, fn ($q) => $q->where('employee_id', $this->request->employee_id));

        $punches = $query->get();

        // Group punches by employee and date, calculate total hours
        $punchData = $punches->groupBy(function ($punch) {
            $istDate = Carbon::parse($punch->punched_in_at)->timezone('Asia/Kolkata')->format('Y-m-d');
            return $punch->employee_id . '-' . $istDate;
        })->map(function ($group) {
            $totalSeconds = 0;
            foreach ($group as $p) {
                if ($p->punched_in_at && $p->punched_out_at) {
                    $in = Carbon::parse($p->punched_in_at)->timezone('Asia/Kolkata');
                    $out = Carbon::parse($p->punched_out_at)->timezone('Asia/Kolkata');
                    $totalSeconds += $in->diffInSeconds($out);
                }
            }
            return [
                'employee_id' => $group->first()->employee_id,
                'date' => Carbon::parse($group->first()->punched_in_at)->timezone('Asia/Kolkata')->format('Y-m-d'),
                'total_hours' => $totalSeconds / 3600, // Convert to hours
            ];
        });

        // Get employees
        $employeesQuery = Employee::with(['department', 'designation']);
        if ($this->request->employee_id) {
            $employeesQuery->where('id', $this->request->employee_id);
        }
        $employees = $employeesQuery->get();

        // Build attendance matrix
        $attendanceMatrix = [];
        foreach ($employees as $employee) {
            $row = [
                'employee_id' => $employee->id,
                'employee_name' => trim($employee->first_name . ' ' . $employee->last_name),
                'department' => $employee->department ? $employee->department->name : '-',
                'designation' => $employee->designation ? $employee->designation->name : '-',
                'attendance' => []
            ];

            foreach ($dates as $date) {
                $key = $employee->id . '-' . $date;
                $status = 'Absent';

                if (isset($punchData[$key])) {
                    $hours = $punchData[$key]['total_hours'];
                    if ($hours >= 9) {
                        $status = 'Present';
                    } elseif ($hours >= 4) {
                        $status = 'Half Day';
                    } else {
                        $status = 'Absent';
                    }
                }

                $row['attendance'][$date] = $status;
            }

            $attendanceMatrix[] = $row;
        }

        return view('exports.attendance', [
            'attendanceMatrix' => $attendanceMatrix,
            'dates' => $dates,
            'startDate' => $startDate->format('F Y'),
        ]);
    }
}
