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
        $employeeId = auth()->user()->id;

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
            ->where('employee_id', $employeeId)
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($app) {
                return [
                    'id' => $app->id,
                    'start_date' => $app->start_date,
                    'end_date' => $app->end_date,
                    'status' => $app->status,
                    'day_type' => $app->day_type ?? 'full',
                    'created_at' => $app->created_at->toISOString(),
                    'leave_type_id' => $app->leave_type_id,
                    'leave_type' => [
                        'id' => $app->leaveType->id,
                        'name' => $app->leaveType->name
                    ]
                ];
            });

        // Calculate statistics (optional enhancement)
        $statistics = [
            'total_applications' => $applications->count(),
            'approved_count' => $applications->where('status', 'approved')->count(),
            'pending_count' => $applications->where('status', 'pending')->count(),
            'rejected_count' => $applications->where('status', 'rejected')->count(),
            'total_leaves_used' => $assignments->sum(function ($assign) {
                return $assign['total_assigned'] - $assign['balance'];
            }),
            'total_leaves_available' => $assignments->sum('balance'),
        ];

        return Inertia::render('Employee/LeaveSummary/Index', [
            'assignments' => $assignments,
            'applications' => $applications,
            'statistics' => $statistics, // Optional - won't break if not used
        ]);
    }
}