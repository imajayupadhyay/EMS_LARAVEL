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
        
        // NEW: Check for warnings/appreciation to show
        $warningData = $this->checkForWarnings($employee);
        
        return Inertia::render('Employee/Dashboard', [
            'workingDays' => $workingDays,
            'totalHours' => $totalHours,
            'remainingLeaves' => $remainingLeaves,
            'isPunchedIn' => $isPunchedIn,
            'punchInTime' => $punchInTime,
            'warningData' => $warningData,  // NEW: Pass warning data to frontend
        ]);
    }
    
    /**
     * NEW METHOD: Check if employee should see any warnings or appreciation messages
     */
    private function checkForWarnings($employee)
    {
        $today = now()->toDateString();
        
        // Get today's punches
        $todayPunches = Punch::where('employee_id', $employee->id)
            ->whereDate('punched_in_at', $today)
            ->orderBy('punched_in_at')
            ->get();
        
        if ($todayPunches->isEmpty()) {
            return null;
        }
        
        $shift = $employee->shift;
        $warningType = null;
        $warningMessage = null;
        $punchId = null;
        $currentCount = 0;
        
        // Check for late arrival (on first punch in of the day)
        $firstPunch = $todayPunches->first();
        if ($firstPunch && !$firstPunch->late_warning_shown) {
            $punchInTime = Carbon::parse($firstPunch->punched_in_at);
            
            if ($shift && $shift->time_from) {
                // Parse shift time
                $timeString = $shift->time_from;
                if ($timeString instanceof \Carbon\Carbon) {
                    $timeString = $timeString->format('H:i:s');
                }
                if (strlen($timeString) > 8) {
                    $timeParts = explode(' ', $timeString);
                    $timeString = end($timeParts);
                }
                
                $shiftStartTime = Carbon::parse($today . ' ' . $timeString);
                $lateThreshold = $shiftStartTime->copy()->addMinutes(10); // 10 minutes grace period
                
                if ($punchInTime->gt($lateThreshold)) {
                    $minutesLate = $shiftStartTime->diffInMinutes($punchInTime);
                    $currentCount = $employee->late_warning_count + 1; // Will be incremented
                    $warningType = 'late';
                    $warningMessage = "You are {$minutesLate} minutes late for your shift. This is warning #{$currentCount}. Please try to arrive on time.";
                    $punchId = $firstPunch->id;
                }
            }
        }
        
        // Check for overtime appreciation (on last punch out of the day)
        // Only check if no late warning needs to be shown
        if (!$warningType) {
            $lastPunchWithOut = $todayPunches->where('punched_out_at', '!=', null)->last();
            
            if ($lastPunchWithOut && !$lastPunchWithOut->overtime_appreciation_shown) {
                $punchOutTime = Carbon::parse($lastPunchWithOut->punched_out_at);
                
                if ($shift && $shift->time_to) {
                    // Parse shift end time
                    $timeString = $shift->time_to;
                    if ($timeString instanceof \Carbon\Carbon) {
                        $timeString = $timeString->format('H:i:s');
                    }
                    if (strlen($timeString) > 8) {
                        $timeParts = explode(' ', $timeString);
                        $timeString = end($timeParts);
                    }
                    
                    $shiftEndTime = Carbon::parse($today . ' ' . $timeString);
                    $overtimeThreshold = $shiftEndTime->copy()->addHour(); // 1 hour overtime
                    
                    if ($punchOutTime->gte($overtimeThreshold)) {
                        $extraMinutes = $shiftEndTime->diffInMinutes($punchOutTime);
                        $extraHours = floor($extraMinutes / 60);
                        $remainingMinutes = $extraMinutes % 60;
                        
                        $currentCount = $employee->overtime_appreciation_count + 1; // Will be incremented
                        $warningType = 'appreciation';
                        $warningMessage = "Great job! You worked {$extraHours} hour(s) and {$remainingMinutes} minute(s) extra today. This is appreciation #{$currentCount}. Your dedication is valued!";
                        $punchId = $lastPunchWithOut->id;
                    }
                }
            }
        }
        
        return [
            'type' => $warningType,
            'message' => $warningMessage,
            'punchId' => $punchId,
            'currentCount' => $currentCount,
        ];
    }
    
    /**
     * NEW METHOD: Mark warning/appreciation as shown and increment counter
     */
    public function markWarningShown(Request $request)
    {
        $validated = $request->validate([
            'punch_id' => 'required|exists:punches,id',
            'type' => 'required|in:late,appreciation',
        ]);
        
        $employee = auth()->guard('employee')->user();
        
        $punch = Punch::where('id', $validated['punch_id'])
            ->where('employee_id', $employee->id)
            ->first();
        
        if (!$punch) {
            return response()->json(['success' => false, 'message' => 'Punch not found'], 404);
        }
        
        // Update punch record to mark warning as shown
        if ($validated['type'] === 'late') {
            $punch->update(['late_warning_shown' => true]);
            // Increment late warning counter for employee
            $employee->increment('late_warning_count');
        } else {
            $punch->update(['overtime_appreciation_shown' => true]);
            // Increment overtime appreciation counter for employee
            $employee->increment('overtime_appreciation_count');
        }
        
        return response()->json([
            'success' => true,
            'current_count' => $validated['type'] === 'late' 
                ? $employee->late_warning_count 
                : $employee->overtime_appreciation_count
        ]);
    }
}