<?php
namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeaveApplication;
use App\Models\LeaveType;
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
            ->where('user_id', auth()->id())
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
        'reason' => 'required|string|max:500'
    ]);

    $leaveApplication = LeaveApplication::create([
        'user_id' => auth()->id(),
        'leave_type_id' => $request->leave_type_id,
        'start_date' => $request->start_date,
        'end_date' => $request->end_date,
        'reason' => $request->reason,
        'status' => 'pending',
    ]);

    // ✅ In-app notification entry (no 'message' column - use 'body')
        AdminNotification::create([
    'title' => 'New Leave Application',
    'message' => auth()->user()->name . ' applied for leave from ' . $request->start_date . ' to ' . $request->end_date,
    'body' => 'Leave application details for admin review.',
    'is_read' => false,
]);

    // ✅ Send mail to admin (works with your mailer setup)
    $adminEmail = 'ajayupadhyay030@gmail.com'; 
    Mail::to($adminEmail)->send(new LeaveApplicationNotificationMail($leaveApplication));

    return back()->with('success', 'Leave application submitted successfully!');
}

    public function update(Request $request, LeaveApplication $leave)
    {
        if ($leave->user_id !== auth()->id() || $leave->created_at->toDateString() !== now()->toDateString()) {
            return back()->with('error', 'You can only edit your today\'s application.');
        }

        $request->validate([
            'leave_type_id' => 'required|exists:leave_types,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string|max:500'
        ]);

        $leave->update([
            'leave_type_id' => $request->leave_type_id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
        ]);

        return back()->with('success', 'Leave application updated successfully!');
    }

    public function destroy(LeaveApplication $leave)
    {
        if ($leave->user_id !== auth()->id() || $leave->created_at->toDateString() !== now()->toDateString()) {
            return back()->with('error', 'You can only delete your today\'s application.');
        }

        $leave->delete();

        return back()->with('success', 'Leave application deleted successfully!');
    }
}
