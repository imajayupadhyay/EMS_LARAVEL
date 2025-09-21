<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Punch;
use App\Models\LeaveAssignment;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Display the employee dashboard with real-time data
     */
    public function index(Request $request)
    {
        $employee = auth()->guard('employee')->user();
        
        // Get current month data
        $currentMonth = now();
        $startOfMonth = $currentMonth->copy()->startOfMonth();
        $endOfMonth = $currentMonth->copy()->endOfMonth();
        
        // Calculate working days (days with punches) this month
        $workingDays = Punch::where('employee_id', $employee->id)
            ->whereBetween('punched_in_at', [$startOfMonth, $endOfMonth])
            ->select('punched_in_at')
            ->get()
            ->groupBy(fn ($punch) => $punch->punched_in_at->toDateString())
            ->count();
        
        // Calculate total hours this month
        $totalSeconds = Punch::where('employee_id', $employee->id)
            ->whereBetween('punched_in_at', [$startOfMonth, $endOfMonth])
            ->whereNotNull('punched_out_at')
            ->get()
            ->sum(function ($punch) {
                if ($punch->punched_in_at && $punch->punched_out_at) {
                    return $punch->punched_in_at->diffInSeconds($punch->punched_out_at);
                }
                return 0;
            });
        
        $totalHours = round($totalSeconds / 3600, 2);
        
        // Calculate remaining leaves
        $leaveAssignments = LeaveAssignment::where('employee_id', $employee->id)->get();
        $remainingLeaves = $leaveAssignments->sum('balance');
        
        // Check if currently punched in (has punch without punch out today)
        $todayPunch = Punch::where('employee_id', $employee->id)
            ->whereDate('punched_in_at', now()->toDateString())
            ->whereNull('punched_out_at')
            ->latest()
            ->first();
        
        $isPunchedIn = $todayPunch !== null;
        
        // Get punch in time if punched in (convert to IST/local timezone)
        $punchInTime = null;
        if ($todayPunch) {
            $punchInTime = Carbon::parse($todayPunch->punched_in_at)
                ->timezone('Asia/Kolkata')
                ->format('h:i A');
        }
        
        return Inertia::render('Employee/Dashboard', [
            'workingDays' => $workingDays,
            'totalHours' => $totalHours,
            'remainingLeaves' => $remainingLeaves,
            'isPunchedIn' => $isPunchedIn,
            'punchInTime' => $punchInTime,
        ]);
    }
}