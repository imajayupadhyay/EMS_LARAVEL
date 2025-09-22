<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Designation;
use App\Models\Department;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DesignationController extends Controller
{
    public function index(Request $request)
    {
        $query = Designation::with(['department']);

        // Apply filters
        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('assignment_status')) {
            if ($request->assignment_status === 'assigned') {
                $query->whereNotNull('department_id');
            } elseif ($request->assignment_status === 'unassigned') {
                $query->whereNull('department_id');
            }
        }

        // Apply sorting
        $sortBy = $request->get('sort_by', 'name_asc');
        switch ($sortBy) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'department_asc':
                $query->leftJoin('departments', 'designations.department_id', '=', 'departments.id')
                      ->orderBy('departments.name', 'asc')
                      ->select('designations.*');
                break;
            case 'department_desc':
                $query->leftJoin('departments', 'designations.department_id', '=', 'departments.id')
                      ->orderByDesc('departments.name')
                      ->select('designations.*');
                break;
            case 'created_desc':
                $query->orderByDesc('created_at');
                break;
            case 'created_asc':
                $query->orderBy('created_at');
                break;
        }

        $designations = $query->paginate(15)->withQueryString();

        // Get additional data for the page
        $departments = Department::select('id', 'name')->orderBy('name')->get();
        $employees = Employee::select('id', 'designation_id')->get(); // For validation

        // Calculate statistics
        $statistics = $this->getStatistics($request);

        return Inertia::render('Admin/Designations/Index', [
            'designations' => $designations,
            'departments' => $departments,
            'employees' => $employees,
            'filters' => $request->only(['search', 'department_id', 'assignment_status', 'sort_by']),
            'statistics' => $statistics,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:designations,name',
            'department_id' => 'nullable|exists:departments,id',
        ], [
            'name.required' => 'Designation name is required.',
            'name.unique' => 'This designation name already exists.',
            'name.max' => 'Designation name cannot exceed 255 characters.',
            'department_id.exists' => 'Selected department does not exist.',
        ]);

        $designation = Designation::create([
            'name' => $request->name,
            'department_id' => $request->department_id,
        ]);

        return back()->with('success', 'Designation created successfully!');
    }

    public function update(Request $request, Designation $designation)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:designations,name,' . $designation->id,
            'department_id' => 'nullable|exists:departments,id',
        ], [
            'name.required' => 'Designation name is required.',
            'name.unique' => 'This designation name already exists.',
            'name.max' => 'Designation name cannot exceed 255 characters.',
            'department_id.exists' => 'Selected department does not exist.',
        ]);

        $designation->update([
            'name' => $request->name,
            'department_id' => $request->department_id,
        ]);

        return back()->with('success', 'Designation updated successfully!');
    }

    public function destroy(Designation $designation)
    {
        // Check if designation has employees assigned
        $employeeCount = Employee::where('designation_id', $designation->id)->count();
        
        if ($employeeCount > 0) {
            return back()->withErrors([
                'designation' => "Cannot delete '{$designation->name}' - it has {$employeeCount} employee(s) assigned."
            ]);
        }

        $designationName = $designation->name;
        $designation->delete();

        return back()->with('success', "Designation '{$designationName}' deleted successfully!");
    }

    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'designation_ids' => 'required|array|min:1',
            'designation_ids.*' => 'exists:designations,id',
            'action' => 'required|in:assign_department,remove_department,delete',
            'department_id' => 'required_if:action,assign_department|nullable|exists:departments,id',
        ]);

        $designationIds = $request->designation_ids;
        $updated = 0;
        $errors = [];

        switch ($request->action) {
            case 'assign_department':
                $updated = Designation::whereIn('id', $designationIds)
                                     ->update(['department_id' => $request->department_id]);
                
                return back()->with('success', "Assigned {$updated} designation(s) to department successfully!");

            case 'remove_department':
                $updated = Designation::whereIn('id', $designationIds)
                                     ->update(['department_id' => null]);
                
                return back()->with('success', "Removed department from {$updated} designation(s) successfully!");

            case 'delete':
                // Check for employees assigned to these designations
                $designationsWithEmployees = Designation::whereIn('id', $designationIds)
                    ->withCount('employees')
                    ->having('employees_count', '>', 0)
                    ->pluck('name')
                    ->toArray();

                if (!empty($designationsWithEmployees)) {
                    return back()->withErrors([
                        'bulk' => 'Cannot delete designations with employees: ' . implode(', ', $designationsWithEmployees)
                    ]);
                }

                $deleted = Designation::whereIn('id', $designationIds)->delete();
                
                return back()->with('success', "Deleted {$deleted} designation(s) successfully!");
        }

        return back()->withErrors(['bulk' => 'Invalid bulk action.']);
    }

    public function bulkAssign(Request $request)
    {
        $request->validate([
            'designation_ids' => 'required|array|min:1',
            'designation_ids.*' => 'exists:designations,id',
            'department_id' => 'required|exists:departments,id',
        ]);

        $updated = Designation::whereIn('id', $request->designation_ids)
                              ->update(['department_id' => $request->department_id]);

        $departmentName = Department::find($request->department_id)->name;

        return back()->with('success', "Assigned {$updated} designation(s) to {$departmentName} successfully!");
    }

    public function bulkDelete(Request $request)
    {
        $request->validate([
            'designation_ids' => 'required|array|min:1',
            'designation_ids.*' => 'exists:designations,id',
        ]);

        // Check for employees assigned to these designations
        $designationsWithEmployees = Designation::whereIn('id', $request->designation_ids)
            ->whereHas('employees')
            ->with('employees')
            ->get();

        if ($designationsWithEmployees->isNotEmpty()) {
            $names = $designationsWithEmployees->pluck('name')->toArray();
            return back()->withErrors([
                'bulk' => 'Cannot delete designations with employees assigned: ' . implode(', ', $names)
            ]);
        }

        $deleted = Designation::whereIn('id', $request->designation_ids)->delete();

        return back()->with('success', "Deleted {$deleted} designation(s) successfully!");
    }

    private function getStatistics(Request $request = null)
    {
        // Apply same filters for consistent statistics
        $query = Designation::query();
        
        if ($request && $request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request && $request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request && $request->filled('assignment_status')) {
            if ($request->assignment_status === 'assigned') {
                $query->whereNotNull('department_id');
            } elseif ($request->assignment_status === 'unassigned') {
                $query->whereNull('department_id');
            }
        }

        $designations = $query->get();
        $totalDesignations = $designations->count();
        $assignedDesignations = $designations->whereNotNull('department_id')->count();
        $unassignedDesignations = $totalDesignations - $assignedDesignations;

        // Get department statistics
        $departmentsWithDesignations = $designations->whereNotNull('department_id')
                                                   ->groupBy('department_id')
                                                   ->count();

        $totalDepartments = Department::count();
        $avgPerDepartment = $totalDepartments > 0 ? round($assignedDesignations / $totalDepartments, 1) : 0;

        return [
            'total_designations' => $totalDesignations,
            'departments_with_designations' => $departmentsWithDesignations,
            'avg_per_department' => $avgPerDepartment,
            'unassigned_count' => $unassignedDesignations,
        ];
    }

    public function getByDepartment(Request $request)
    {
        $departmentId = $request->department_id;
        
        $designations = Designation::where('department_id', $departmentId)
                                  ->orderBy('name')
                                  ->get();

        return response()->json($designations);
    }

    public function assignToDepartment(Request $request)
    {
        $request->validate([
            'designation_id' => 'required|exists:designations,id',
            'department_id' => 'required|exists:departments,id',
        ]);

        $designation = Designation::find($request->designation_id);
        $designation->update(['department_id' => $request->department_id]);

        $departmentName = Department::find($request->department_id)->name;

        return back()->with('success', "'{$designation->name}' assigned to {$departmentName} successfully!");
    }
}