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
        $user = auth()->user();
        $month = $request->get('month', now()->format('Y-m'));

        $punches = Punch::where('user_id', $user->id)
            ->whereMonth('punched_in_at', Carbon::parse($month)->month)
            ->whereYear('punched_in_at', Carbon::parse($month)->year)
            ->orderByDesc('punched_in_at')
            ->get()
            ->groupBy(fn($item) => $item->punched_in_at->toDateString())
            ->map(function ($dayPunches) {
                $firstIn = $dayPunches->min('punched_in_at');
                $lastOut = $dayPunches->max('punched_out_at');

                $totalSeconds = $dayPunches->sum(function ($punch) {
                    return $punch->punched_out_at?->diffInSeconds($punch->punched_in_at) ?? 0;
                });

                return [
                    'date' => $firstIn->toDateString(),
                    'first_in' => $firstIn->format('H:i'),
                    'last_out' => $lastOut?->format('H:i') ?? '--',
                    'total_hours' => gmdate('H:i', $totalSeconds)
                ];
            })
            ->values();

        return Inertia::render('Employee/Attendance/Index', [
            'records' => $punches,
            'month' => $month
        ]);
    }
}
