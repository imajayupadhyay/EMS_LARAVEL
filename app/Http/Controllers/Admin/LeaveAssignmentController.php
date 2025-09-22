<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveAssignment;
use App\Models\LeaveType;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Designation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class LeaveAssignmentController extends Controller
{
    public function index(Request $request)
    {
        $query = LeaveAssignment::with(['employee.department', 'employee.designation', 'leaveType']);

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('department_id')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        if ($request->filled('designation_id')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('designation_id', $request->designation_id);
            });
        }

        if ($request->filled('leave_type_id')) {
            $query->where('leave_type_id', $request->leave_type_id);
        }

        if ($request->filled('status')) {
            switch ($request->status) {
                case 'no_balance':
                    $query->where('balance', '<=', 0);
                    break;
                case 'low_balance':
                    $query->whereRaw('balance <= (total_assigned * 0.2)');
                    break;
                case 'full_balance':
                    $query->whereColumn('balance', 'total_assigned');
                    break;
            }
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $allowedSorts = ['created_at', 'total_assigned', 'balance', 'employee_name'];
        if (in_array($sortBy, $allowedSorts)) {
            if ($sortBy === 'employee_name') {
                $query->join('employees', 'leave_assignments.employee_id', '=', 'employees.id')
                      ->orderBy('employees.first_name', $sortOrder)
                      ->select('leave_assignments.*');
            } else {
                $query->orderBy($sortBy, $sortOrder);
            }
        }

        $assignments = $query->paginate(15)->withQueryString();

        // Get summary statistics
        $stats = $this->getStatistics($request);

        return Inertia::render('Admin/LeaveAssignments/Index', [
            'assignments' => $assignments,
            'employees' => Employee::select('id', 'first_name', 'last_name', 'email', 'department_id', 'designation_id')
                                   ->with(['department:id,name', 'designation:id,name'])
                                   ->orderBy('first_name')
                                   ->get(),
            'leaveTypes' => LeaveType::select('id', 'name', 'allowed_days')->get(),
            'departments' => Department::select('id', 'name')->orderBy('name')->get(),
            'designations' => Designation::select('id', 'name')->orderBy('name')->get(),
            'filters' => $request->only(['search', 'department_id', 'designation_id', 'leave_type_id', 'status', 'sort_by', 'sort_order']),
            'statistics' => $stats,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'total_assigned' => 'required|integer|min:0|max:365',
            'balance' => 'required|integer|min:0|max:365',
        ], [
            'employee_id.required' => 'Please select an employee.',
            'leave_type_id.required' => 'Please select a leave type.',
            'total_assigned.min' => 'Total assigned must be at least 0.',
            'balance.min' => 'Balance must be at least 0.',
            'total_assigned.max' => 'Total assigned cannot exceed 365 days.',
        ]);

        // Check if assignment already exists
        $existing = LeaveAssignment::where('employee_id', $request->employee_id)
                                   ->where('leave_type_id', $request->leave_type_id)
                                   ->first();

        if ($existing) {
            return back()->withErrors(['employee_id' => 'Leave assignment already exists for this employee and leave type.']);
        }

        // Validate balance is not greater than total assigned
        if ($request->balance > $request->total_assigned) {
            return back()->withErrors(['balance' => 'Balance cannot be greater than total assigned.']);
        }

        LeaveAssignment::create($request->all());

        return back()->with('success', 'Leave assignment created successfully!');
    }

    public function update(Request $request, LeaveAssignment $leaveAssignment)
    {
        $request->validate([
            'total_assigned' => 'required|integer|min:0|max:365',
            'balance' => 'required|integer|min:0|max:365',
        ]);

        // Validate balance is not greater than total assigned
        if ($request->balance > $request->total_assigned) {
            return back()->withErrors(['balance' => 'Balance cannot be greater than total assigned.']);
        }

        $leaveAssignment->update($request->only('total_assigned', 'balance'));

        return back()->with('success', 'Leave assignment updated successfully!');
    }

    public function destroy(LeaveAssignment $leaveAssignment)
    {
        $employeeName = $leaveAssignment->employee->first_name . ' ' . $leaveAssignment->employee->last_name;
        $leaveTypeName = $leaveAssignment->leaveType->name;
        
        $leaveAssignment->delete();

        return back()->with('success', "Leave assignment for {$employeeName} ({$leaveTypeName}) deleted successfully!");
    }

    public function bulkAssign(Request $request)
    {
        $request->validate([
            'employee_ids' => 'required|array|min:1',
            'employee_ids.*' => 'exists:employees,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'total_assigned' => 'required|integer|min:0|max:365',
            'balance' => 'required|integer|min:0|max:365',
        ]);

        if ($request->balance > $request->total_assigned) {
            return back()->withErrors(['balance' => 'Balance cannot be greater than total assigned.']);
        }

        $created = 0;
        $skipped = 0;

        foreach ($request->employee_ids as $employeeId) {
            $existing = LeaveAssignment::where('employee_id', $employeeId)
                                       ->where('leave_type_id', $request->leave_type_id)
                                       ->exists();

            if (!$existing) {
                LeaveAssignment::create([
                    'employee_id' => $employeeId,
                    'leave_type_id' => $request->leave_type_id,
                    'total_assigned' => $request->total_assigned,
                    'balance' => $request->balance,
                ]);
                $created++;
            } else {
                $skipped++;
            }
        }

        $message = "Bulk assignment completed! Created: {$created}";
        if ($skipped > 0) {
            $message .= ", Skipped: {$skipped} (already existed)";
        }

        return back()->with('success', $message);
    }

    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'assignment_ids' => 'required|array|min:1',
            'assignment_ids.*' => 'exists:leave_assignments,id',
            'action' => 'required|in:adjust_balance,reset_balance',
            'adjustment_value' => 'required_if:action,adjust_balance|integer',
        ]);

        $updated = 0;

        if ($request->action === 'adjust_balance') {
            $assignments = LeaveAssignment::whereIn('id', $request->assignment_ids)->get();
            
            foreach ($assignments as $assignment) {
                $newBalance = max(0, min($assignment->total_assigned, $assignment->balance + $request->adjustment_value));
                $assignment->update(['balance' => $newBalance]);
                $updated++;
            }
        } elseif ($request->action === 'reset_balance') {
            LeaveAssignment::whereIn('id', $request->assignment_ids)
                           ->update(['balance' => DB::raw('total_assigned')]);
            $updated = count($request->assignment_ids);
        }

        return back()->with('success', "Updated {$updated} leave assignments successfully!");
    }

    private function getStatistics(Request $request)
    {
        $query = LeaveAssignment::with(['employee.department', 'employee.designation', 'leaveType']);

        // Apply same filters as main query for consistent stats
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('employee', function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('department_id')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('department_id', $request->department_id);
            });
        }

        if ($request->filled('designation_id')) {
            $query->whereHas('employee', function ($q) use ($request) {
                $q->where('designation_id', $request->designation_id);
            });
        }

        if ($request->filled('leave_type_id')) {
            $query->where('leave_type_id', $request->leave_type_id);
        }

        $assignments = $query->get();

        return [
            'total_assignments' => $assignments->count(),
            'total_allocated' => $assignments->sum('total_assigned'),
            'total_remaining' => $assignments->sum('balance'),
            'total_used' => $assignments->sum(function ($assignment) {
                return $assignment->total_assigned - $assignment->balance;
            }),
            'no_balance_count' => $assignments->where('balance', '<=', 0)->count(),
            'low_balance_count' => $assignments->filter(function ($assignment) {
                return $assignment->balance > 0 && $assignment->balance <= ($assignment->total_assigned * 0.2);
            })->count(),
        ];
    }
}