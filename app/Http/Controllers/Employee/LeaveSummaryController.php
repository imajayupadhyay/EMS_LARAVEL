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
        $userId = Auth::id();

        // Fetch leave assignments for logged in employee
        $assignments = LeaveAssignment::with('leaveType')
            ->where('user_id', $userId)
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

        // Fetch leave applications
        $applications = LeaveApplication::with('leaveType')
            ->where('user_id', $userId)
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
