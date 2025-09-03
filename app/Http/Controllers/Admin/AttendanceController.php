<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Punch;
use App\Models\Employee;
use Carbon\Carbon;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceExport;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $selectedEmployee = $request->get('employee_id');
        $selectedDate = $request->get('date');
        $selectedMonth = $request->get('month') ?? now()->format('Y-m');

       $query = Punch::with(['employee.designation', 'employee.department'])
    ->when($selectedEmployee, fn ($q) => $q->where('employee_id', $selectedEmployee))
    ->when($selectedDate, fn ($q) => $q->whereDate('punched_in_at', $selectedDate))
    ->when($selectedMonth, fn ($q) => $q->whereMonth('punched_in_at', Carbon::parse($selectedMonth)->month)
                                       ->whereYear('punched_in_at', Carbon::parse($selectedMonth)->year));

$punches = $query->get();

$attendance = $punches->groupBy(function ($punch) {
    return $punch->employee_id . '-' . Carbon::parse($punch->punched_in_at)->format('Y-m-d');
})->map(function ($group) {
    $first = $group->first();

    // ✅ Convert to IST
    $firstIn = $group->min('punched_in_at') ? Carbon::parse($group->min('punched_in_at'))->timezone('Asia/Kolkata') : null;
    $lastOut = $group->max('punched_out_at') ? Carbon::parse($group->max('punched_out_at'))->timezone('Asia/Kolkata') : null;

    // ✅ Total time calculation (summing up all in-out durations)
    $total = 0;
    foreach ($group as $punch) {
        if ($punch->punched_in_at && $punch->punched_out_at) {
            $in = Carbon::parse($punch->punched_in_at)->timezone('Asia/Kolkata');
            $out = Carbon::parse($punch->punched_out_at)->timezone('Asia/Kolkata');
            $total += $in->diffInSeconds($out);
        }
    }

   return [
    'employee_id'  => $first->employee->id, // ✅ Add this
    'employee'     => $first->employee->first_name . ' ' . $first->employee->last_name,
    'department'   => $first->employee->department->name ?? '—',
    'designation'  => $first->employee->designation->name ?? '—',
    'date'         => Carbon::parse($first->punched_in_at)->timezone('Asia/Kolkata')->format('Y-m-d'),
    'first_in'     => $firstIn ? $firstIn->format('h:i:s A') : '—',
    'last_out'     => $lastOut ? $lastOut->format('h:i:s A') : '—',
    'hours'        => gmdate('H:i:s', $total),
];

})->values();

        $totalWorkingDays = $punches->groupBy(function ($punch) {
            return Carbon::parse($punch->punched_in_at)->format('Y-m-d');
        })->count();

        return Inertia::render('Admin/Attendance/Index', [
            'attendance' => $attendance,
            'employees' => Employee::select('id', 'first_name', 'last_name')->get(),
            'totalWorkingDays' => $totalWorkingDays,
            'filters' => [
                'employee_id' => $selectedEmployee,
                'date' => $selectedDate,
                'month' => $selectedMonth,
            ]
        ]);
    }

    public function export(Request $request)
    {
        return Excel::download(new AttendanceExport($request), 'attendance.xlsx');
    }

    public function details($employeeId, $date)
{
    $punches = Punch::where('employee_id', $employeeId)
        ->whereDate('punched_in_at', $date)
        ->orderBy('punched_in_at')
        ->get();

    if ($punches->isEmpty()) {
        return response()->json([]);
    }

    $firstIn = $punches->first()->punched_in_at->timezone('Asia/Kolkata')->format('h:i:s A');
    $lastOut = ($punches->last()->punched_out_at ?? now())
                ->timezone('Asia/Kolkata')->format('h:i:s A');

    $totalSeconds = 0;
    $pairs = $punches->chunk(2);
    foreach ($pairs as $pair) {
        if (isset($pair[0]) && isset($pair[1])) {
            $in = Carbon::parse($pair[0]->punched_in_at);
            $out = Carbon::parse($pair[1]->punched_out_at ?? now());
            $totalSeconds += $in->diffInSeconds($out);
        }
    }

    return response()->json([
        'employee' => $punches->first()->employee->first_name . ' ' . $punches->first()->employee->last_name,
        'date'     => $date,
        'first_in' => $firstIn,
        'last_out' => $lastOut,
        'hours'    => gmdate('H:i:s', $totalSeconds),
    ]);
}

}
