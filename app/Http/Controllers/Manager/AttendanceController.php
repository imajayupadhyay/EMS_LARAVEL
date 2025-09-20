<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Punch;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Designation;
use Carbon\Carbon;
use Inertia\Inertia;
use Illuminate\Pagination\LengthAwarePaginator;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ManagerAttendanceExport;

class AttendanceController extends Controller
{
    /**
     * Display attendance records with filters
     */
    public function index(Request $request)
    {
        $selectedEmployee = $request->get('employee_id');
        $selectedDepartment = $request->get('department_id');
        $selectedDesignation = $request->get('designation_id');
        $selectedDate = $request->get('date');
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');
        $selectedMonth = $request->get('month') ?? now()->format('Y-m');

        // Build base query with filters
        $query = Punch::with(['employee.designation', 'employee.department'])
            ->when($selectedEmployee, fn ($q) => $q->where('employee_id', $selectedEmployee))
            ->when($selectedDate, fn ($q) => $q->whereDate('punched_in_at', $selectedDate))
            ->when($fromDate && $toDate, function ($q) use ($fromDate, $toDate) {
                $q->whereBetween('punched_in_at', [$fromDate, $toDate]);
            })
            ->when($selectedMonth && !$fromDate && !$toDate, function ($q) use ($selectedMonth) {
                $m = Carbon::parse($selectedMonth);
                $q->whereMonth('punched_in_at', $m->month)
                  ->whereYear('punched_in_at', $m->year);
            });

        // Apply department/designation filters via employee relationship
        if ($selectedDepartment || $selectedDesignation) {
            $query->whereHas('employee', function ($q) use ($selectedDepartment, $selectedDesignation) {
                if ($selectedDepartment) {
                    $q->where('department_id', $selectedDepartment);
                }
                if ($selectedDesignation) {
                    $q->where('designation_id', $selectedDesignation);
                }
            });
        }

        $punches = $query->get();

        // Group by employee + IST date and calculate attendance
        $attendance = $punches->groupBy(function ($punch) {
            $istDate = Carbon::parse($punch->punched_in_at)->timezone('Asia/Kolkata')->format('Y-m-d');
            return $punch->employee_id . '-' . $istDate;
        })->map(function ($group) {
            $first = $group->first();

            // Calculate first in and last out in IST
            $firstInAt = $group->min('punched_in_at');
            $lastOutAt = $group->filter(fn($p) => !is_null($p->punched_out_at))->max('punched_out_at');

            $firstIn = $firstInAt ? Carbon::parse($firstInAt)->timezone('Asia/Kolkata') : null;
            $lastOut = $lastOutAt ? Carbon::parse($lastOutAt)->timezone('Asia/Kolkata') : null;

            // Calculate total hours
            $totalSeconds = 0;
            foreach ($group as $p) {
                if ($p->punched_in_at && $p->punched_out_at) {
                    $in = Carbon::parse($p->punched_in_at)->timezone('Asia/Kolkata');
                    $out = Carbon::parse($p->punched_out_at)->timezone('Asia/Kolkata');
                    $totalSeconds += $in->diffInSeconds($out);
                }
            }

            $emp = $first->employee ?? null;

            return [
                'employee_id' => $first->employee_id,
                'employee' => $emp ? trim(($emp->first_name ?? '') . ' ' . ($emp->last_name ?? '')) : '',
                'department' => $emp && $emp->department ? $emp->department->name : '—',
                'designation' => $emp && $emp->designation ? $emp->designation->name : '—',
                'date' => Carbon::parse($first->punched_in_at)->timezone('Asia/Kolkata')->format('Y-m-d'),
                'first_in' => $firstIn ? $firstIn->format('h:i:s A') : '',
                'last_out' => $lastOut ? $lastOut->format('h:i:s A') : '',
                'hours' => gmdate('H:i:s', $totalSeconds),
                'total_seconds' => $totalSeconds,
            ];
        })->values();

        // Separate today's attendance
        $todayIst = Carbon::now('Asia/Kolkata')->format('Y-m-d');
        
        if (!empty($selectedDate) || (!empty($fromDate) && !empty($toDate))) {
            // If specific date filter, paginate everything
            $sorted = $attendance->sortByDesc('date')->values();
            
            $perPage = (int)($request->get('per_page', 20));
            $page = (int)($request->get('page', 1));
            
            $paginated = new LengthAwarePaginator(
                $sorted->forPage($page, $perPage)->values(),
                $sorted->count(),
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );
            
            return Inertia::render('Manager/Attendance/Index', [
                'todayAttendance' => [],
                'attendance' => $paginated,
                'employees' => Employee::with(['department', 'designation'])
                    ->select('id', 'first_name', 'last_name', 'department_id', 'designation_id')
                    ->orderBy('first_name')
                    ->get(),
                'departments' => Department::orderBy('name')->get(['id', 'name']),
                'designations' => Designation::orderBy('name')->get(['id', 'name']),
                'filters' => [
                    'employee_id' => $selectedEmployee,
                    'department_id' => $selectedDepartment,
                    'designation_id' => $selectedDesignation,
                    'date' => $selectedDate,
                    'from_date' => $fromDate,
                    'to_date' => $toDate,
                    'month' => $selectedMonth,
                ],
            ]);
        }

        // No specific date filter - show today's and paginate rest
        $todayAttendance = $attendance->filter(fn ($r) => $r['date'] === $todayIst)->values();
        $rest = $attendance->reject(fn ($r) => $r['date'] === $todayIst)->values()
            ->sortByDesc('date')->values();

        $perPage = (int)($request->get('per_page', 20));
        $page = (int)($request->get('page', 1));

        $paginated = new LengthAwarePaginator(
            $rest->forPage($page, $perPage)->values(),
            $rest->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return Inertia::render('Manager/Attendance/Index', [
            'todayAttendance' => $todayAttendance,
            'attendance' => $paginated,
            'employees' => Employee::with(['department', 'designation'])
                ->select('id', 'first_name', 'last_name', 'department_id', 'designation_id')
                ->orderBy('first_name')
                ->get(),
            'departments' => Department::orderBy('name')->get(['id', 'name']),
            'designations' => Designation::orderBy('name')->get(['id', 'name']),
            'filters' => [
                'employee_id' => $selectedEmployee,
                'department_id' => $selectedDepartment,
                'designation_id' => $selectedDesignation,
                'date' => $selectedDate,
                'from_date' => $fromDate,
                'to_date' => $toDate,
                'month' => $selectedMonth,
            ],
        ]);
    }

    /**
     * Export attendance records
     */
    public function export(Request $request)
    {
        return Excel::download(new ManagerAttendanceExport($request), 'attendance_' . now()->format('Y-m-d') . '.xlsx');
    }

    /**
     * Get attendance details for specific employee and date
     */
    public function details($employeeId, $date)
    {
        $ist = 'Asia/Kolkata';

        $startIst = Carbon::createFromFormat('Y-m-d', $date, $ist)->startOfDay();
        $endIst = (clone $startIst)->endOfDay();

        $startUtc = $startIst->copy()->timezone('UTC')->format('Y-m-d H:i:s');
        $endUtc = $endIst->copy()->timezone('UTC')->format('Y-m-d H:i:s');

        $startIstStr = $startIst->format('Y-m-d H:i:s');
        $endIstStr = $endIst->format('Y-m-d H:i:s');

        $punches = Punch::where('employee_id', $employeeId)
            ->where(function ($q) use ($date, $startUtc, $endUtc, $startIstStr, $endIstStr) {
                $q->whereBetween('punched_in_at', [$startUtc, $endUtc])
                    ->orWhereRaw("CONVERT_TZ(punched_in_at, '+00:00', '+05:30') BETWEEN ? AND ?", [$startIstStr, $endIstStr])
                    ->orWhereDate('punched_in_at', $date);
            })
            ->orderBy('punched_in_at')
            ->get();

        $emp = Employee::with(['department', 'designation'])
            ->select('id', 'first_name', 'last_name', 'department_id', 'designation_id')
            ->find($employeeId);

        $deptName = $emp && $emp->department ? $emp->department->name : '';
        $desigName = $emp && $emp->designation ? $emp->designation->name : '';

        if ($punches->isEmpty()) {
            return response()->json([
                'not_found' => true,
                'employee' => $emp ? trim(($emp->first_name ?? '') . ' ' . ($emp->last_name ?? '')) : '',
                'date' => $date,
                'department' => $deptName,
                'designation' => $desigName,
                'first_in' => '',
                'last_out' => '',
                'hours' => '',
                'intervals' => [],
                'total_seconds' => 0,
            ]);
        }

        // Build intervals
        $intervals = $punches->map(function ($p) use ($ist) {
            $punchIn = $p->punched_in_at ? Carbon::parse($p->punched_in_at)->timezone($ist) : null;
            $punchOut = $p->punched_out_at ? Carbon::parse($p->punched_out_at)->timezone($ist) : null;

            $seconds = ($punchIn && $punchOut) ? $punchIn->diffInSeconds($punchOut) : 0;

            return [
                'punch_in' => $punchIn ? $punchIn->format('h:i:s A') : '',
                'punch_out' => $punchOut ? $punchOut->format('h:i:s A') : 'Not Punched Out',
                'duration' => gmdate('H:i:s', $seconds),
            ];
        });

        $firstInAt = $punches->min('punched_in_at');
        $firstIn = $firstInAt ? Carbon::parse($firstInAt)->timezone($ist) : null;

        $lastOutAt = $punches->filter(fn($p) => !is_null($p->punched_out_at))->max('punched_out_at');
        $lastOut = $lastOutAt ? Carbon::parse($lastOutAt)->timezone($ist) : null;

        $totalSeconds = 0;
        foreach ($punches as $p) {
            if ($p->punched_in_at && $p->punched_out_at) {
                $in = Carbon::parse($p->punched_in_at)->timezone($ist);
                $out = Carbon::parse($p->punched_out_at)->timezone($ist);
                $totalSeconds += $in->diffInSeconds($out);
            }
        }

        return response()->json([
            'not_found' => false,
            'employee' => $emp ? trim(($emp->first_name ?? '') . ' ' . ($emp->last_name ?? '')) : '',
            'date' => $date,
            'department' => $deptName,
            'designation' => $desigName,
            'first_in' => $firstIn ? $firstIn->format('h:i:s A') : '',
            'last_out' => $lastOut ? $lastOut->format('h:i:s A') : '',
            'hours' => gmdate('H:i:s', $totalSeconds),
            'intervals' => $intervals,
            'total_seconds' => $totalSeconds,
        ]);
    }
}