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

        // Base punches query (filters applied to the list itself)
        $query = Punch::with(['employee.designation', 'employee.department'])
            ->when($selectedEmployee, fn ($q) => $q->where('employee_id', $selectedEmployee))
            ->when($selectedDate, fn ($q) => $q->whereDate('punched_in_at', $selectedDate))
            ->when($selectedMonth, function ($q) use ($selectedMonth) {
                $m = Carbon::parse($selectedMonth);
                $q->whereMonth('punched_in_at', $m->month)
                  ->whereYear('punched_in_at', $m->year);
            });

        $punches = $query->get();

        // Group by employee + IST date
        $attendance = $punches->groupBy(function ($punch) {
            $istDate = Carbon::parse($punch->punched_in_at)->timezone('Asia/Kolkata')->format('Y-m-d');
            return $punch->employee_id . '-' . $istDate;
        })->map(function ($group) {
            $first = $group->first();

            // First in (min in) + last out (max non-null out) in IST
            $firstInAt = $group->min('punched_in_at');
            $lastOutAt = $group->filter(fn($p) => !is_null($p->punched_out_at))->max('punched_out_at');

            $firstIn = $firstInAt ? Carbon::parse($firstInAt)->timezone('Asia/Kolkata') : null;
            $lastOut = $lastOutAt ? Carbon::parse($lastOutAt)->timezone('Asia/Kolkata') : null;

            // Sum complete intervals only
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
                'first_in'    => $firstIn ? $firstIn->format('h:i:s A') : '',
                'last_out'    => $lastOut ? $lastOut->format('h:i:s A') : '',  // blank if not punched out
                'hours'       => gmdate('H:i:s', $totalSeconds),
            ];
        })->values();

        // -------- Total Working Days (only when an employee is selected) --------
        $totalWorkingDays = null;
        if (!empty($selectedEmployee)) {
            $m = Carbon::parse($selectedMonth);
            // Pull all punches for that employee in that month (regardless of selectedDate)
            $employeeMonthPunches = Punch::where('employee_id', $selectedEmployee)
                ->whereMonth('punched_in_at', $m->month)
                ->whereYear('punched_in_at', $m->year)
                ->pluck('punched_in_at');

            // Count unique IST dates
            $uniqueIstDates = $employeeMonthPunches->map(function ($dt) {
                return Carbon::parse($dt)->timezone('Asia/Kolkata')->format('Y-m-d');
            })->unique();

            $totalWorkingDays = $uniqueIstDates->count();
        }

        // -------- Always show TODAY fully (when no specific date filter), paginate the rest --------
        $todayIst = Carbon::now('Asia/Kolkata')->format('Y-m-d');

        // If a specific date is set, just paginate everything
        if (!empty($selectedDate)) {
            $sorted = $attendance->sortByDesc('date')->values();

            $perPage = (int)($request->get('per_page', 20));
            $page    = (int)($request->get('page', 1));

            $paginated = new LengthAwarePaginator(
                $sorted->forPage($page, $perPage)->values(),
                $sorted->count(),
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );

            return Inertia::render('Admin/Attendance/Index', [
                'todayAttendance'  => [],
                'attendance'       => $paginated,
                'employees'        => Employee::select('id', 'first_name', 'last_name')->get(),
                'totalWorkingDays' => $totalWorkingDays, // null unless employee selected
                'filters'          => [
                    'employee_id' => $selectedEmployee,
                    'date'        => $selectedDate,
                    'month'       => $selectedMonth,
                ],
            ]);
        }

        // No date filter → split today vs rest
        $todayAttendance = $attendance->filter(fn ($r) => $r['date'] === $todayIst)->values();
        $rest            = $attendance->reject(fn ($r) => $r['date'] === $todayIst)->values()
                                      ->sortByDesc('date')->values();

        $perPage = (int)($request->get('per_page', 20));
        $page    = (int)($request->get('page', 1));

        $paginated = new LengthAwarePaginator(
            $rest->forPage($page, $perPage)->values(),
            $rest->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return Inertia::render('Admin/Attendance/Index', [
            'todayAttendance'  => $todayAttendance,
            'attendance'       => $paginated,
            'employees'        => Employee::select('id', 'first_name', 'last_name')->get(),
            'totalWorkingDays' => $totalWorkingDays, // null unless employee selected
            'filters'          => [
                'employee_id' => $selectedEmployee,
                'date'        => $selectedDate,
                'month'       => $selectedMonth,
            ],
        ]);
    }

    public function export(Request $request)
    {
        return Excel::download(new AttendanceExport($request), 'attendance.xlsx');
    }

public function details($employeeId, $date)
{
    $ist = 'Asia/Kolkata';

    // Build day windows
    $startIst = \Carbon\Carbon::createFromFormat('Y-m-d', $date, $ist)->startOfDay();
    $endIst   = (clone $startIst)->endOfDay();

    // Convert those to UTC (for the standard TIMESTAMP-in-UTC storage)
    $startUtc = $startIst->copy()->timezone('UTC')->format('Y-m-d H:i:s');
    $endUtc   = $endIst->copy()->timezone('UTC')->format('Y-m-d H:i:s');

    // As strings for CONVERT_TZ branch (works without named TZ tables because we use fixed offsets)
    $startIstStr = $startIst->format('Y-m-d H:i:s');
    $endIstStr   = $endIst->format('Y-m-d H:i:s');

    $punches = Punch::with('employee:id,first_name,last_name')
        ->where('employee_id', $employeeId)
        ->where(function ($q) use ($date, $startUtc, $endUtc, $startIstStr, $endIstStr) {
            $q
            // Case A: DB stores TIMESTAMP effectively in UTC
            ->whereBetween('punched_in_at', [$startUtc, $endUtc])

            // OR Case B: Compare by viewing column in IST inside SQL
            ->orWhereRaw(
                "CONVERT_TZ(punched_in_at, '+00:00', '+05:30') BETWEEN ? AND ?",
                [$startIstStr, $endIstStr]
            )

            // OR Case C: Session/column behaves as local (IST) so date() matches
            ->orWhereDate('punched_in_at', $date);
        })
        ->orderBy('punched_in_at')
        ->get();

    if ($punches->isEmpty()) {
        return response()->json([
            'not_found'     => true,
            'employee'      => '',
            'date'          => $date,
            'first_in'      => '',
            'last_out'      => '',
            'hours'         => '',
            'intervals'     => [],
            'total_seconds' => 0,
        ]);
    }

    // Build response in IST
    $firstInAt = $punches->min('punched_in_at');
    $firstIn   = $firstInAt ? \Carbon\Carbon::parse($firstInAt)->timezone($ist)->format('h:i:s A') : '';

    $lastOutAt = $punches->filter(fn ($p) => !is_null($p->punched_out_at))->max('punched_out_at');
    $lastOut   = $lastOutAt ? \Carbon\Carbon::parse($lastOutAt)->timezone($ist)->format('h:i:s A') : '';

    $intervals = [];
    $totalSeconds = 0;

    foreach ($punches as $p) {
        if (!$p->punched_in_at) continue;

        $in  = \Carbon\Carbon::parse($p->punched_in_at)->timezone($ist);
        $out = $p->punched_out_at ? \Carbon\Carbon::parse($p->punched_out_at)->timezone($ist) : null;

        $dur = $out ? $in->diffInSeconds($out) : 0; // open interval not counted
        $totalSeconds += $dur;

        $intervals[] = [
            'in'    => $in->format('h:i:s A'),
            'out'   => $out ? $out->format('h:i:s A') : '',
            'hours' => $out ? gmdate('H:i:s', $dur) : '',
        ];
    }

    return response()->json([
        'employee'      => $punches->first()->employee->first_name . ' ' . $punches->first()->employee->last_name,
        'date'          => $date,
        'first_in'      => $firstIn,
        'last_out'      => $lastOut,
        'hours'         => gmdate('H:i:s', $totalSeconds),
        'intervals'     => $intervals,
        'total_seconds' => $totalSeconds,
    ]);
}



}
