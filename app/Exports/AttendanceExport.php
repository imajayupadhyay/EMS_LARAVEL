<?php

namespace App\Exports;

use App\Models\Punch;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\Log;

class AttendanceExport implements FromView
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        try {
            // Increase memory limit and execution time for export
            ini_set('memory_limit', '512M');
            ini_set('max_execution_time', '300');

            // Determine date range
            $fromDate = $this->request->from_date ?? $this->request->get('from_date');
            $toDate = $this->request->to_date ?? $this->request->get('to_date');
            $month = $this->request->month ?? $this->request->get('month');

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

            // Limit date range to prevent memory issues (max 3 months)
            $daysDiff = $startDate->diffInDays($endDate);
            if ($daysDiff > 92) {
                $endDate = $startDate->copy()->addDays(92);
            }

            // Get all dates in range
            $dates = [];
            $currentDate = $startDate->copy();
            while ($currentDate <= $endDate) {
                $dates[] = $currentDate->format('Y-m-d');
                $currentDate->addDay();
            }

            // Get employee ID from request
            $employeeId = $this->request->employee_id ?? $this->request->get('employee_id');

            // Query punches for the date range - chunk for memory efficiency
            $query = Punch::with(['employee.designation', 'employee.department'])
                ->whereBetween('punched_in_at', [$startDate->copy()->startOfDay(), $endDate->copy()->endOfDay()])
                ->when($employeeId, fn ($q) => $q->where('employee_id', $employeeId));

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
            if ($employeeId) {
                $employeesQuery->where('id', $employeeId);
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
                    'attendance' => [],
                    'total_days' => 0,
                    'present_days' => 0
                ];

                foreach ($dates as $date) {
                    $key = $employee->id . '-' . $date;
                    $status = 'Absent';

                    if (isset($punchData[$key])) {
                        $hours = $punchData[$key]['total_hours'];
                        // Treat 4+ hours as present (including half days)
                        if ($hours >= 4) {
                            $status = 'Present';
                            $row['present_days']++;
                        } else {
                            $status = 'Absent';
                        }
                    }

                    $row['attendance'][$date] = $status;
                    $row['total_days']++;
                }

                $attendanceMatrix[] = $row;
            }

            return view('exports.attendance', [
                'attendanceMatrix' => $attendanceMatrix,
                'dates' => $dates,
                'startDate' => $startDate->format('F Y'),
            ]);

        } catch (\Exception $e) {
            // Log error for debugging
            Log::error('Attendance Export Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $this->request->all()
            ]);

            // Return empty view with error message
            return view('exports.attendance', [
                'attendanceMatrix' => [],
                'dates' => [],
                'startDate' => 'Error',
                'error' => 'Failed to generate report: ' . $e->getMessage()
            ]);
        }
    }
}
