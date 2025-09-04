<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Punch;
use App\Models\Employee;
use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Pagination\LengthAwarePaginator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceExport;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $selectedEmployee = $request->get('employee_id');
        $selectedDate     = $request->get('date');
        $selectedMonth    = $request->get('month') ?? now()->format('Y-m');

        // Base punches query
        $query = Punch::with(['employee.designation', 'employee.department'])
            ->when($selectedEmployee, fn ($q) => $q->where('employee_id', $selectedEmployee))
            ->when($selectedDate, fn ($q) => $q->whereDate('punched_in_at', $selectedDate))
            ->when($selectedMonth, function ($q) use ($selectedMonth) {
                $m = Carbon::parse($selectedMonth);
                $q->whereMonth('punched_in_at', $m->month)
                  ->whereYear('punched_in_at', $m->year);
            });

        $punches = $query->get();

        // Group by employee + date (IST)
        $attendance = $punches->groupBy(function ($punch) {
            $istDate = Carbon::parse($punch->punched_in_at)->timezone('Asia/Kolkata')->format('Y-m-d');
            return $punch->employee_id . '-' . $istDate;
        })->map(function ($group) {
            $first = $group->first();

            // First in (min punched_in_at) and last out (max punched_out_at) in IST
            $firstInAt = $group->min('punched_in_at');
            $lastOutAt = $group->filter(fn($p) => !is_null($p->punched_out_at))->max('punched_out_at');

            $firstIn = $firstInAt
                ? Carbon::parse($firstInAt)->timezone('Asia/Kolkata')
                : null;
            $lastOut = $lastOutAt
                ? Carbon::parse($lastOutAt)->timezone('Asia/Kolkata')
                : null;

            // Sum durations from complete in-out rows only
            $totalSeconds = 0;
            foreach ($group as $p) {
                if ($p->punched_in_at && $p->punched_out_at) {
                    $in  = Carbon::parse($p->punched_in_at)->timezone('Asia/Kolkata');
                    $out = Carbon::parse($p->punched_out_at)->timezone('Asia/Kolkata');
                    $totalSeconds += $in->diffInSeconds($out);
                }
            }

            return [
                'employee_id' => $first->employee->id,
                'employee'    => trim(($first->employee->first_name ?? '') . ' ' . ($first->employee->last_name ?? '')),
                'department'  => $first->employee->department->name ?? '—',
                'designation' => $first->employee->designation->name ?? '—',
                'date'        => Carbon::parse($first->punched_in_at)->timezone('Asia/Kolkata')->format('Y-m-d'),
                'first_in'    => $firstIn ? $firstIn->format('h:i:s A') : '',  // blank if none
                'last_out'    => $lastOut ? $lastOut->format('h:i:s A') : '',  // blank if none
                'hours'       => gmdate('H:i:s', $totalSeconds),
            ];
        })->values();

        // Total working days (unique IST dates in this dataset)
        $totalWorkingDays = $punches->groupBy(function ($punch) {
            return Carbon::parse($punch->punched_in_at)->timezone('Asia/Kolkata')->format('Y-m-d');
        })->count();

        // Always show "today" (IST) entries fully; paginate the rest
        $todayIst = Carbon::now('Asia/Kolkata')->format('Y-m-d');

        // If a specific date filter is applied, just paginate everything normally
        if (!empty($selectedDate)) {
            // Sort by date desc then employee for consistency
            $sorted = $attendance->sortByDesc('date')->values();

            $perPage = (int)($request->get('per_page', 20));
            $page    = (int)($request->get('page', 1));

            $paginated = new LengthAwarePaginator(
                $sorted->forPage($page, $perPage)->values(),
                $sorted->count(),
                $perPage,
                $page,
                [
                    'path'  => $request->url(),
                    'query' => $request->query(),
                ]
            );

            return Inertia::render('Admin/Attendance/Index', [
                'todayAttendance'  => [], // none specially shown when date filter is used
                'attendance'       => $paginated,
                'employees'        => Employee::select('id', 'first_name', 'last_name')->get(),
                'totalWorkingDays' => $totalWorkingDays,
                'filters'          => [
                    'employee_id' => $selectedEmployee,
                    'date'        => $selectedDate,
                    'month'       => $selectedMonth,
                ],
            ]);
        }

        // No specific date filter: split today vs rest
        $todayAttendance = $attendance->filter(fn ($r) => $r['date'] === $todayIst)->values();
        $rest            = $attendance->reject(fn ($r) => $r['date'] === $todayIst)->values();

        // Sort "rest" by date desc then employee for stable display
        $rest = $rest->sortByDesc('date')->values();

        $perPage = (int)($request->get('per_page', 20));
        $page    = (int)($request->get('page', 1));

        $paginated = new LengthAwarePaginator(
            $rest->forPage($page, $perPage)->values(),
            $rest->count(),
            $perPage,
            $page,
            [
                'path'  => $request->url(),
                'query' => $request->query(),
            ]
        );

        return Inertia::render('Admin/Attendance/Index', [
            'todayAttendance'  => $todayAttendance,
            'attendance'       => $paginated, // paginated older days
            'employees'        => Employee::select('id', 'first_name', 'last_name')->get(),
            'totalWorkingDays' => $totalWorkingDays,
            'filters'          => [
                'employee_id' => $selectedEmployee,
                'date'        => $selectedDate,
                'month'       => $selectedMonth,
            ],
        ]);
    }

    public function export(Request $request)
    {
        // Keeps your existing export behavior
        return Excel::download(new AttendanceExport($request), 'attendance.xlsx');
    }

    public function details($employeeId, $date)
    {
        $punches = Punch::with('employee:id,first_name,last_name')
            ->where('employee_id', $employeeId)
            ->whereDate('punched_in_at', $date)
            ->orderBy('punched_in_at')
            ->get();

        if ($punches->isEmpty()) {
            return response()->json([]);
        }

        $ist = 'Asia/Kolkata';

        // First IN (IST) — blank if none
        $firstInAt = $punches->min('punched_in_at');
        $firstIn   = $firstInAt
            ? Carbon::parse($firstInAt)->timezone($ist)->format('h:i:s A')
            : '';

        // Last OUT (IST) — blank if none
        $lastOutAt = $punches->filter(fn ($p) => !is_null($p->punched_out_at))
                             ->max('punched_out_at');

        $lastOut = $lastOutAt
            ? Carbon::parse($lastOutAt)->timezone($ist)->format('h:i:s A')
            : '';

        // Build intervals and total; DO NOT count open intervals
        $intervals    = [];
        $totalSeconds = 0;

        foreach ($punches as $p) {
            if (!$p->punched_in_at) {
                continue;
            }

            $in  = Carbon::parse($p->punched_in_at)->timezone($ist);
            $out = $p->punched_out_at
                ? Carbon::parse($p->punched_out_at)->timezone($ist)
                : null;

            $durSeconds = $out ? $in->diffInSeconds($out) : 0;
            $totalSeconds += $durSeconds;

            $intervals[] = [
                'in'    => $in->format('h:i:s A'),
                'out'   => $out ? $out->format('h:i:s A') : '',             // blank when not punched out
                'hours' => $out ? gmdate('H:i:s', $durSeconds) : '',        // blank for open interval
            ];
        }

        return response()->json([
            'employee'      => $punches->first()->employee->first_name . ' ' . $punches->first()->employee->last_name,
            'date'          => Carbon::parse($date)->format('Y-m-d'),
            'first_in'      => $firstIn,
            'last_out'      => $lastOut,
            'hours'         => gmdate('H:i:s', $totalSeconds),
            'intervals'     => $intervals,
            'total_seconds' => $totalSeconds,
        ]);
    }
}
