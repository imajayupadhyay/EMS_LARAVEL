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

        $month = $request->get('month', now()->format('Y-m'));

        $punches = Punch::where('employee_id', $employee->id)
            ->whereMonth('punched_in_at', Carbon::parse($month)->month)
            ->whereYear('punched_in_at', Carbon::parse($month)->year)
            ->orderBy('punched_in_at')
            ->get();

        // Grouping & raw data: we send raw punches & let Vue handle formatting if you want
        $records = $punches->groupBy(fn ($punch) => $punch->punched_in_at->toDateString())
            ->map(function ($dayPunches) {
                return [
                    'date' => $dayPunches->first()->punched_in_at->toDateString(),
                    'first_in' => $dayPunches->min('punched_in_at'),
                    'last_out' => $dayPunches->max('punched_out_at'),
                    'total_seconds' => $dayPunches->sum(function ($punch) {
                        return $punch->punched_out_at?->diffInSeconds($punch->punched_in_at) ?? 0;
                    }),
                ];
            })
            ->values();

        return Inertia::render('Employee/Attendance/Index', [
            'records' => $records,
            'month' => $month,
        ]);
    }
}
