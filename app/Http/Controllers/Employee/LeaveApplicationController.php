<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeaveApplication;
use App\Models\LeaveType;
use App\Models\LeaveAssignment;
use App\Models\AdminNotification;
use App\Mail\LeaveApplicationNotificationMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class LeaveApplicationController extends Controller
{
    public function index()
    {
        $leaveTypes = LeaveType::all();
        $applications = LeaveApplication::with('leaveType')
            ->where('employee_id', auth()->user()->id)
            ->orderByDesc('created_at')
            ->paginate(10);

        // NEW: Get leave balances for the employee (optional enhancement)
        $leaveBalances = LeaveAssignment::with('leaveType')
            ->where('employee_id', auth()->user()->id)
            ->get()
            ->mapWithKeys(function ($assignment) {
                return [
                    $assignment->leave_type_id => [
                        'name' => $assignment->leaveType->name,
                        'total_assigned' => $assignment->total_assigned,
                        'balance' => $assignment->balance,
                    ]
                ];
            });

        return inertia('Employee/LeaveApplication/Index', [
            'leaveTypes' => $leaveTypes,
            'applications' => $applications,
            'leaveBalances' => $leaveBalances, // NEW: Optional - won't break if not used
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:500',
            'day_type' => 'required|in:full,half',
        ]);

        // Check 12-hour advance notice requirement
        $startDateTime = Carbon::parse($request->start_date);
        $now = Carbon::now();

        // If the leave starts within the next 12 hours, reject the request
        if ($startDateTime->lessThan($now->addHours(12))) {
            return back()->with('error', 'Leave applications must be submitted at least 12 hours in advance. Please contact your manager directly for urgent leave requests.');
        }

        // PRESERVED: Original calculation logic
        $deduct = $request->day_type === 'half' ? 0.5 : 1;

        // PRESERVED: Fetch assignment and deduct balance
        $assignment = LeaveAssignment::where('employee_id', auth()->user()->id)
            ->where('leave_type_id', $request->leave_type_id)
            ->first();

        if ($assignment) {
            $assignment->balance = max(0, $assignment->balance - $deduct);
            $assignment->save();
        }

        // PRESERVED: Create leave application with exact same logic
        $leaveApplication = LeaveApplication::create([
            'employee_id' => auth()->user()->id,
            'leave_type_id' => $request->leave_type_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'status' => 'pending',
            'day_type' => $request->day_type,
        ]);

        // PRESERVED: Original notification creation
        AdminNotification::create([
            'title' => 'New Leave Application',
            'message' => auth()->user()->first_name . ' ' . auth()->user()->last_name . ' applied for a ' . ucfirst($request->day_type) . ' day leave.',
            'body' => 'Leave from ' . $request->start_date . ' to ' . $request->end_date,
            'is_read' => false,
        ]);

        // PRESERVED: Original email logic
        $adminEmail = 'Goelanmol1802@gmail.com';
        Mail::to($adminEmail)->send(new LeaveApplicationNotificationMail($leaveApplication));

        return back()->with('success', 'Leave application submitted successfully!');
    }

    public function update(Request $request, LeaveApplication $leave)
    {
        // Check if user owns the application and if it's still pending
        if ($leave->employee_id !== auth()->user()->id) {
            return back()->with('error', 'You can only edit your own applications.');
        }

        if ($leave->status !== 'pending') {
            return back()->with('error', 'You can only edit pending applications.');
        }

        // PRESERVED: Original validation rules
        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:500',
            'day_type' => 'required|in:full,half',
        ]);

        // Check 12-hour advance notice requirement for updates
        $startDateTime = Carbon::parse($request->start_date);
        $now = Carbon::now();

        // If the leave starts within the next 12 hours, reject the update
        if ($startDateTime->lessThan($now->addHours(12))) {
            return back()->with('error', 'Leave applications must be submitted at least 12 hours in advance. Please contact your manager directly for urgent leave requests.');
        }

        // PRESERVED: Original update logic
        $leave->update([
            'leave_type_id' => $request->leave_type_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'day_type' => $request->day_type,
        ]);

        return back()->with('success', 'Leave application updated successfully!');
    }

    public function destroy(LeaveApplication $leave)
    {
        // Check if user owns the application and if it's still pending
        if ($leave->employee_id !== auth()->user()->id) {
            return back()->with('error', 'You can only delete your own applications.');
        }

        if ($leave->status !== 'pending') {
            return back()->with('error', 'You can only delete pending applications.');
        }

        // PRESERVED: Original delete logic
        $leave->delete();

        return back()->with('success', 'Leave application deleted successfully!');
    }
}