<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LeaveApplication;
use App\Models\LeaveAssignment;
use App\Models\LeaveType;
use App\Models\Employee;
use Carbon\Carbon;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ManagerLeaveExport;

class LeaveController extends Controller
{
    /**
     * Display leave applications with filters
     */
    public function index(Request $request)
    {
        $selectedEmployee = $request->get('employee_id');
        $selectedStatus = $request->get('status');
        $selectedLeaveType = $request->get('leave_type_id');
        $selectedDate = $request->get('date');
        $fromDate = $request->get('from_date');
        $toDate = $request->get('to_date');

        // Build query with filters
        $query = LeaveApplication::with(['employee.department', 'employee.designation', 'leaveType'])
            ->when($selectedEmployee, fn ($q) => $q->where('employee_id', $selectedEmployee))
            ->when($selectedStatus, fn ($q) => $q->where('status', $selectedStatus))
            ->when($selectedLeaveType, fn ($q) => $q->where('leave_type_id', $selectedLeaveType))
            ->when($selectedDate, fn ($q) => $q->whereDate('start_date', $selectedDate))
            ->when($fromDate && $toDate, function ($q) use ($fromDate, $toDate) {
                $q->where(function($query) use ($fromDate, $toDate) {
                    $query->whereBetween('start_date', [$fromDate, $toDate])
                          ->orWhereBetween('end_date', [$fromDate, $toDate])
                          ->orWhere(function($q) use ($fromDate, $toDate) {
                              $q->where('start_date', '<=', $fromDate)
                                ->where('end_date', '>=', $toDate);
                          });
                });
            });

        $leaves = $query->latest()->paginate(15)->withQueryString();

        // Get leave summary for selected employee
        $leaveSummary = null;
        if ($selectedEmployee) {
            $employee = Employee::find($selectedEmployee);
            $leaveSummary = $this->getEmployeeLeaveSummary($selectedEmployee);
            $leaveSummary['employee_name'] = $employee ? trim($employee->first_name . ' ' . $employee->last_name) : '';
        }

        return Inertia::render('Manager/Leaves/Index', [
            'leaves' => $leaves,
            'employees' => Employee::with(['department', 'designation'])
                ->select('id', 'first_name', 'last_name', 'department_id', 'designation_id')
                ->orderBy('first_name')
                ->get(),
            'leaveTypes' => LeaveType::orderBy('name')->get(['id', 'name']),
            'leaveSummary' => $leaveSummary,
            'filters' => [
                'employee_id' => $selectedEmployee,
                'status' => $selectedStatus,
                'leave_type_id' => $selectedLeaveType,
                'date' => $selectedDate,
                'from_date' => $fromDate,
                'to_date' => $toDate,
            ],
        ]);
    }

    /**
     * Get leave summary for an employee
     */
    protected function getEmployeeLeaveSummary($employeeId)
    {
        $assignments = LeaveAssignment::with('leaveType')
            ->where('employee_id', $employeeId)
            ->get();

        $totalAssigned = $assignments->sum('total_assigned');
        $totalBalance = $assignments->sum('balance');
        $totalUsed = $totalAssigned - $totalBalance;

        // Leave breakdown by type
        $leaveBreakdown = $assignments->map(function($assignment) {
            return [
                'leave_type' => $assignment->leaveType->name ?? 'Unknown',
                'total' => $assignment->total_assigned,
                'used' => $assignment->total_assigned - $assignment->balance,
                'balance' => $assignment->balance,
            ];
        });

        // Recent leaves
        $recentLeaves = LeaveApplication::where('employee_id', $employeeId)
            ->with('leaveType')
            ->latest()
            ->take(5)
            ->get()
            ->map(function($leave) {
                return [
                    'id' => $leave->id,
                    'leave_type' => $leave->leaveType->name ?? 'Unknown',
                    'start_date' => Carbon::parse($leave->start_date)->format('d M Y'),
                    'end_date' => Carbon::parse($leave->end_date)->format('d M Y'),
                    'days' => Carbon::parse($leave->start_date)->diffInDays(Carbon::parse($leave->end_date)) + 1,
                    'status' => $leave->status,
                    'reason' => $leave->reason,
                ];
            });

        return [
            'total_assigned' => $totalAssigned,
            'total_used' => $totalUsed,
            'total_balance' => $totalBalance,
            'leave_breakdown' => $leaveBreakdown,
            'recent_leaves' => $recentLeaves,
        ];
    }

    /**
     * Update leave status (approve/reject)
     */
    public function updateStatus(Request $request, LeaveApplication $leave)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);

        if ($leave->status !== 'pending') {
            return back()->with('error', 'This leave request has already been processed.');
        }

        $leave->update(['status' => $request->status]);

        // Update leave balance if approved
        if ($request->status === 'approved') {
            $start = Carbon::parse($leave->start_date);
            $end = Carbon::parse($leave->end_date);
            $days = $start->diffInDays($end) + 1;

            $assignment = LeaveAssignment::where('employee_id', $leave->employee_id)
                ->where('leave_type_id', $leave->leave_type_id)
                ->first();

            if ($assignment && $assignment->balance >= $days) {
                $assignment->decrement('balance', $days);
            }
        }

        return back()->with('success', 'Leave request ' . $request->status . ' successfully!');
    }

    /**
     * Export leave data
     */
    public function export(Request $request)
    {
        return Excel::download(
            new ManagerLeaveExport($request), 
            'leaves_' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    /**
     * Get leave details
     */
    public function details($leaveId)
    {
        $leave = LeaveApplication::with(['employee.department', 'employee.designation', 'leaveType'])
            ->findOrFail($leaveId);

        $employee = $leave->employee;
        
        $days = Carbon::parse($leave->start_date)->diffInDays(Carbon::parse($leave->end_date)) + 1;

        return response()->json([
            'id' => $leave->id,
            'employee' => $employee ? trim($employee->first_name . ' ' . $employee->last_name) : '',
            'department' => $employee && $employee->department ? $employee->department->name : '',
            'designation' => $employee && $employee->designation ? $employee->designation->name : '',
            'leave_type' => $leave->leaveType->name ?? 'Unknown',
            'start_date' => Carbon::parse($leave->start_date)->format('d M Y'),
            'end_date' => Carbon::parse($leave->end_date)->format('d M Y'),
            'days' => $days,
            'day_type' => $leave->day_type ?? 'full_day',
            'status' => $leave->status,
            'reason' => $leave->reason,
            'applied_at' => $leave->created_at->format('d M Y, h:i A'),
        ]);
    }
}