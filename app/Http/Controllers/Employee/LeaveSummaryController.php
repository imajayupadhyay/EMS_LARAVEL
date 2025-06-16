<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\LeaveAssignment;
use App\Models\LeaveApplication;
use Inertia\Inertia;

class LeaveSummaryController extends Controller
{
    
    public function index()
{
    $employeeId = auth()->user()->id;  // Get logged-in employee ID

    // Fetch leave assignments for logged-in employee
    $assignments = LeaveAssignment::with('leaveType')
        ->where('employee_id', $employeeId)
        ->get()
        ->map(function ($assign) {
            return [
                'id' => $assign->id,
                'total_assigned' => $assign->total_assigned,
                'balance' => $assign->balance,
                'leave_type' => [
                    'id' => $assign->leaveType->id,
                    'name' => $assign->leaveType->name
                ]
            ];
        });

    // Fetch leave applications for logged-in employee
    $applications = LeaveApplication::with('leaveType')
        ->where('employee_id', $employeeId)  // ✅ Updated from user_id
        ->get()
        ->map(function ($app) {
            return [
                'id' => $app->id,
                'start_date' => $app->start_date,
                'end_date' => $app->end_date,
                'status' => $app->status,
                'leave_type' => [
                    'id' => $app->leaveType->id,
                    'name' => $app->leaveType->name
                ]
            ];
        });

    return Inertia::render('Employee/LeaveSummary/Index', [
        'assignments' => $assignments,
        'applications' => $applications
    ]);
}

}
