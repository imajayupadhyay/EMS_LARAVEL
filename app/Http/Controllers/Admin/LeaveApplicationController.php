<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveApplication;
use App\Models\LeaveAssignment;
use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class LeaveApplicationController extends Controller
{
    public function index(Request $request)
    {
        $applications = LeaveApplication::with(['user', 'leaveType'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->employee, fn($q) => $q->whereHas('user', fn($q2) => $q2->where('name', 'like', "%{$request->employee}%")))
            ->when($request->leave_type, fn($q) => $q->whereHas('leaveType', fn($q2) => $q2->where('name', $request->leave_type)))
            ->when($request->date, fn($q) => $q->whereDate('start_date', $request->date))
            ->latest()
            ->paginate(9)
            ->withQueryString();

        $leaveTypes = LeaveType::all(['id', 'name']);

        return inertia('Admin/LeaveApplications/Index', [
            'applications' => $applications,
            'leaveTypes' => $leaveTypes,
            'filters' => $request->only(['status', 'employee', 'leave_type', 'date'])
        ]);
    }

    public function update(Request $request, LeaveApplication $leaveApplication)
    {
        $request->validate(['status' => 'required|in:approved,rejected']);

        if ($leaveApplication->status !== 'pending') {
            return back()->with('error', 'Action already taken on this leave.');
        }

        $leaveApplication->update(['status' => $request->status]);

        
if ($request->status === 'approved') {
    $start = Carbon::parse($leaveApplication->start_date);
    $end = Carbon::parse($leaveApplication->end_date);
    $days = $start->diffInDays($end) + 1;

    $assignment = LeaveAssignment::where('user_id', $leaveApplication->user_id)
        ->where('leave_type_id', $leaveApplication->leave_type_id)
        ->first();

    if ($assignment) {
        $assignment->decrement('balance', $days);
    }
}

        return back()->with('success', 'Leave ' . $request->status . ' successfully!');
    }
}
