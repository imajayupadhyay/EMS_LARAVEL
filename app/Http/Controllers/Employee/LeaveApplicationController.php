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

class LeaveApplicationController extends Controller
{
    public function index()
    {
        $leaveTypes = LeaveType::all();
        $applications = LeaveApplication::with('leaveType')
            ->where('employee_id', auth()->user()->id)
            ->orderByDesc('created_at')
            ->paginate(10);

        return inertia('Employee/LeaveApplication/Index', [
            'leaveTypes' => $leaveTypes,
            'applications' => $applications
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

        $deduct = $request->day_type === 'half' ? 0.5 : 1;

        // Fetch assignment
        $assignment = LeaveAssignment::where('employee_id', auth()->user()->id)
            ->where('leave_type_id', $request->leave_type_id)
            ->first();

        if ($assignment) {
            $assignment->balance = max(0, $assignment->balance - $deduct);
            $assignment->save();
        }

        // Create leave application
        $leaveApplication = LeaveApplication::create([
            'employee_id' => auth()->user()->id,
            'leave_type_id' => $request->leave_type_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'status' => 'pending',
            'day_type' => $request->day_type,
        ]);

        // Notification
        AdminNotification::create([
            'title' => 'New Leave Application',
            'message' => auth()->user()->first_name . ' ' . auth()->user()->last_name . ' applied for a ' . ucfirst($request->day_type) . ' day leave.',
            'body' => 'Leave from ' . $request->start_date . ' to ' . $request->end_date,
            'is_read' => false,
        ]);

        // Email
        $adminEmail = 'ajayupadhyay030@gmail.com';
        Mail::to($adminEmail)->send(new LeaveApplicationNotificationMail($leaveApplication));

        return back()->with('success', 'Leave application submitted successfully!');
    }

    public function update(Request $request, LeaveApplication $leave)
    {
        if ($leave->employee_id !== auth()->user()->id || $leave->created_at->toDateString() !== now()->toDateString()) {
            return back()->with('error', 'You can only edit your today\'s application.');
        }

        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:500',
            'day_type' => 'required|in:full,half',
        ]);

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
        if ($leave->employee_id !== auth()->user()->id || $leave->created_at->toDateString() !== now()->toDateString()) {
            return back()->with('error', 'You can only delete your today\'s application.');
        }

        $leave->delete();

        return back()->with('success', 'Leave application deleted successfully!');
    }
}
