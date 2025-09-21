<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Punch;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $employee = auth()->user();

        // Default to current month
        $month = $request->get('month', now()->format('Y-m'));
        
        // Date range filter
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        
        // Status filter (present, late, etc.)
        $status = $request->get('status');

        $query = Punch::where('employee_id', $employee->id);

        // Apply filters
        if ($startDate && $endDate) {
            // Date range takes precedence over month filter
            $query->whereDate('punched_in_at', '>=', $startDate)
                  ->whereDate('punched_in_at', '<=', $endDate);
        } elseif ($month) {
            // Month filter
            $query->whereMonth('punched_in_at', Carbon::parse($month)->month)
                  ->whereYear('punched_in_at', Carbon::parse($month)->year);
        }

        $punches = $query->orderBy('punched_in_at')
            ->get();

        // Grouping & calculating attendance records
        $records = $punches->groupBy(fn ($punch) => $punch->punched_in_at->toDateString())
            ->map(function ($dayPunches) use ($employee) {
                $firstIn = $dayPunches->min('punched_in_at');
                $lastOut = $dayPunches->max('punched_out_at');
                $totalSeconds = $dayPunches->sum(function ($punch) {
                    return $punch->punched_out_at?->diffInSeconds($punch->punched_in_at) ?? 0;
                });

                // Calculate status (on-time, late, absent)
                $status = $this->calculateStatus($firstIn, $employee);

                return [
                    'date' => $dayPunches->first()->punched_in_at->toDateString(),
                    'day_of_week' => $dayPunches->first()->punched_in_at->format('l'),
                    'first_in' => $firstIn,
                    'last_out' => $lastOut,
                    'total_seconds' => $totalSeconds,
                    'total_hours' => round($totalSeconds / 3600, 2),
                    'status' => $status,
                    'punch_count' => $dayPunches->count(),
                ];
            })
            ->values();

        // Apply status filter if provided
        if ($status) {
            $records = $records->filter(function ($record) use ($status) {
                return $record['status'] === $status;
            })->values();
        }

        // Calculate summary statistics
        $summary = [
            'total_days' => $records->count(),
            'total_hours' => $records->sum('total_hours'),
            'on_time_days' => $records->where('status', 'on-time')->count(),
            'late_days' => $records->where('status', 'late')->count(),
            'average_hours' => $records->count() > 0 ? round($records->avg('total_hours'), 2) : 0,
        ];

        return Inertia::render('Employee/Attendance/Index', [
            'records' => $records,
            'month' => $month,
            'filters' => [
                'month' => $month,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => $status,
            ],
            'summary' => $summary,
        ]);
    }

    /**
     * Calculate attendance status based on shift timing
     * FIXED VERSION - Handles date parsing correctly
     */
    private function calculateStatus($punchInTime, $employee)
    {
        if (!$punchInTime) return 'absent';

        try {
            // Get employee's shift timing
            $shift = $employee->shift ?? null;
            
            // Get the date portion from punch in time
            $dateString = $punchInTime->format('Y-m-d');
            
            if (!$shift || !$shift->time_from) {
                // Default: consider 9:30 AM as start time
                $shiftStartTime = Carbon::parse($dateString . ' 09:30:00');
            } else {
                // Handle different formats of time_from
                $timeString = $shift->time_from;
                
                // If it's a Carbon instance, format it
                if ($timeString instanceof \Carbon\Carbon) {
                    $timeString = $timeString->format('H:i:s');
                }
                
                // Clean the time string (remove any date parts)
                if (strlen($timeString) > 8) {
                    // If it contains a full datetime, extract just the time
                    $timeParts = explode(' ', $timeString);
                    $timeString = end($timeParts);
                }
                
                // Combine date and time properly
                $shiftStartTime = Carbon::parse($dateString . ' ' . $timeString);
            }

            // Grace period of 15 minutes
            $gracePeriod = 15;
            $lateThreshold = $shiftStartTime->copy()->addMinutes($gracePeriod);

            if ($punchInTime->lte($lateThreshold)) {
                return 'on-time';
            } else {
                return 'late';
            }
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::warning('Failed to calculate attendance status: ' . $e->getMessage(), [
                'employee_id' => $employee->id,
                'punch_time' => $punchInTime,
                'shift' => $shift ?? null
            ]);
            
            // Return default status
            return 'present';
        }
    }

    /**
     * Export attendance data
     */
    public function export(Request $request)
    {
        $employee = auth()->user();
        $month = $request->get('month', now()->format('Y-m'));

        $punches = Punch::where('employee_id', $employee->id)
            ->whereMonth('punched_in_at', Carbon::parse($month)->month)
            ->whereYear('punched_in_at', Carbon::parse($month)->year)
            ->orderBy('punched_in_at')
            ->get();

        $records = $punches->groupBy(fn ($punch) => $punch->punched_in_at->toDateString())
            ->map(function ($dayPunches) {
                return [
                    'Date' => $dayPunches->first()->punched_in_at->format('Y-m-d'),
                    'Day' => $dayPunches->first()->punched_in_at->format('l'),
                    'First In' => $dayPunches->min('punched_in_at')->format('H:i:s'),
                    'Last Out' => $dayPunches->max('punched_out_at')?->format('H:i:s') ?? '--',
                    'Total Hours' => round($dayPunches->sum(function ($punch) {
                        return $punch->punched_out_at?->diffInSeconds($punch->punched_in_at) ?? 0;
                    }) / 3600, 2),
                ];
            });

        $filename = "attendance_{$employee->first_name}_{$month}.csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($records) {
            $file = fopen('php://output', 'w');
            
            // Add headers
            if ($records->count() > 0) {
                fputcsv($file, array_keys($records->first()));
                
                // Add data
                foreach ($records as $record) {
                    fputcsv($file, $record);
                }
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}