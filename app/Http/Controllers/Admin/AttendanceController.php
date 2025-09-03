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
    $total = 0;

    // ⏱ Calculate first in and last out
    $firstIn = $group->min('punched_in_at');
    $lastOut = $group->max('punched_out_at');

    // ⏱ Calculate total worked hours (your existing logic)
    $pairs = $group->chunk(2);
    foreach ($pairs as $pair) {
        if (isset($pair[0]) && isset($pair[1])) {
            $in = Carbon::parse($pair[0]->punched_in_at);
            $out = Carbon::parse($pair[1]->punched_out_at ?? now());
            $total += $in->diffInSeconds($out);
        }
    }

    return [
        'employee'     => $first->employee->first_name . ' ' . $first->employee->last_name,
        'department'   => $first->employee->department->name ?? '—',
        'designation'  => $first->employee->designation->name ?? '—',
        'date'         => Carbon::parse($first->punched_in_at)->format('Y-m-d'),
        'first_in'     => $firstIn ? Carbon::parse($firstIn)->format('H:i:s') : '—',
        'last_out'     => $lastOut ? Carbon::parse($lastOut)->format('H:i:s') : '—',
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
}
