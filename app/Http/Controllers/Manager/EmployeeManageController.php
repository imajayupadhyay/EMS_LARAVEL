<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Designation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class EmployeeManageController extends Controller
{
    /**
     * Display a listing of employees with optional filters.
     */
    public function index(Request $request)
    {
        $query = Employee::with(['department', 'designation']);

        // Search by name (first/middle/last or combined) or email
        if ($request->filled('name')) {
            $term = '%' . $request->input('name') . '%';
            $query->where(function ($q) use ($term) {
                $q->where('first_name', 'like', $term)
                    ->orWhere('middle_name', 'like', $term)
                    ->orWhere('last_name', 'like', $term)
                    ->orWhere('email', 'like', $term)
                    ->orWhereRaw("concat(first_name, ' ', last_name) LIKE ?", [$term])
                    ->orWhereRaw("concat(first_name, ' ', middle_name, ' ', last_name) LIKE ?", [$term]);
            });
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('designation_id')) {
            $query->where('designation_id', $request->designation_id);
        }

        // Safe ordering and pagination (15 per page)
        $employees = $query->orderBy('first_name')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Manager/Employees/Index', [
            'employees' => $employees,
            'departments' => Department::orderBy('name')->get(['id', 'name']),
            'designations' => Designation::orderBy('name')->get(['id', 'name']),
            'filters' => $request->only('name', 'department_id', 'designation_id'),
            'canCreate' => true, // Manager can create employees
            'canEdit' => true,   // Manager can edit employees
            'canDelete' => true, // Manager can delete employees
        ]);
    }

    /**
     * Show the form for creating a new employee.
     */
    public function create()
    {
        return Inertia::render('Manager/Employees/Create', [
            'departments' => Department::orderBy('name')->get(['id', 'name']),
            'designations' => Designation::orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Store a newly created employee in storage.
     * Note: Managers cannot set salary information or employee codes.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:employees,email',
            'password' => 'required|string|min:8|confirmed',
            'contact' => 'required|string|max:255',
            'emergency_contact' => 'nullable|string|max:255',
            'gender' => 'nullable|in:male,female,other',
            'dob' => 'nullable|date|before:today',
            'doj' => 'nullable|date',
            'marital_status' => 'nullable|in:single,married,divorced,widowed',
            'address' => 'nullable|string|max:1000',
            'zip' => 'nullable|string|max:50',
            'pay_scale' => 'nullable|string|max:255',
            'work_location' => 'nullable|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'designation_id' => 'required|exists:designations,id',
            // Removed: employee_code and all salary fields
        ]);

        // Hash the password
        $validated['password'] = Hash::make($validated['password']);

        Employee::create($validated);

        return redirect()->route('manager.employees.index')
            ->with('success', 'Employee created successfully!');
    }

    /**
     * Return employee details (AJAX JSON) — exclude salary/payroll fields for managers.
     */
    public function show(Employee $employee)
    {
        $employee->load(['department', 'designation']);

        $formatDate = function ($val) {
            if ($val === null) return null;
            if ($val instanceof \DateTimeInterface) return $val->format('Y-m-d');
            try {
                return \Carbon\Carbon::parse($val)->toDateString();
            } catch (\Throwable $e) {
                return (string) $val;
            }
        };

        $data = [
            'id' => $employee->id,
            'first_name' => $employee->first_name,
            'middle_name' => $employee->middle_name,
            'last_name' => $employee->last_name,
            'full_name' => trim($employee->first_name . ' ' . ($employee->middle_name ? $employee->middle_name . ' ' : '') . $employee->last_name),
            'email' => $employee->email,
            'contact' => $employee->contact,
            'emergency_contact' => $employee->emergency_contact,
            'gender' => $employee->gender,
            'dob' => $formatDate($employee->dob),
            'doj' => $formatDate($employee->doj),
            'marital_status' => $employee->marital_status,
            'address' => $employee->address,
            'zip' => $employee->zip,
            'pay_scale' => $employee->pay_scale,
            'work_location' => $employee->work_location,
            // Removed: employee_code and all salary fields

            'department' => $employee->department ? [
                'id' => $employee->department->id,
                'name' => $employee->department->name,
            ] : null,
            'designation' => $employee->designation ? [
                'id' => $employee->designation->id,
                'name' => $employee->designation->name,
            ] : null,

            'created_at' => $employee->created_at?->toDateTimeString(),
            'updated_at' => $employee->updated_at?->toDateTimeString(),
        ];

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * Show the form for editing the specified employee.
     */
    public function edit(Employee $employee)
    {
        $employee->load(['department', 'designation']);

        return Inertia::render('Manager/Employees/Edit', [
            'employee' => $employee,
            'departments' => Department::orderBy('name')->get(['id', 'name']),
            'designations' => Designation::orderBy('name')->get(['id', 'name']),
        ]);
    }

    /**
     * Update the specified employee in storage.
     * Note: Managers cannot update salary information or employee codes.
     */
    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('employees')->ignore($employee->id)
            ],
            'contact' => 'required|string|max:255',
            'emergency_contact' => 'nullable|string|max:255',
            'gender' => 'nullable|in:male,female,other',
            'dob' => 'nullable|date|before:today',
            'doj' => 'nullable|date',
            'marital_status' => 'nullable|in:single,married,divorced,widowed',
            'address' => 'nullable|string|max:1000',
            'zip' => 'nullable|string|max:50',
            'pay_scale' => 'nullable|string|max:255',
            'work_location' => 'nullable|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'designation_id' => 'required|exists:designations,id',
            // Removed: employee_code and all salary fields
        ]);

        // Only update password if provided
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'string|min:8|confirmed',
            ]);
            $validated['password'] = Hash::make($request->password);
        }

        $employee->update($validated);

        return redirect()->route('manager.employees.index')
            ->with('success', 'Employee updated successfully!');
    }

    /**
     * Remove the specified employee from storage.
     */
    public function destroy(Employee $employee)
    {
        try {
            $employee->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Employee deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to delete employee. They may have associated records.'
            ], 422);
        }
    }

    /**
     * Bulk delete employees.
     */
    public function bulkDelete(Request $request)
    {
        $validated = $request->validate([
            'employee_ids' => 'required|array|min:1',
            'employee_ids.*' => 'exists:employees,id',
        ]);

        try {
            $deletedCount = Employee::whereIn('id', $validated['employee_ids'])->delete();
            
            return response()->json([
                'success' => true,
                'message' => "Successfully deleted {$deletedCount} employees."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to delete some employees. They may have associated records.'
            ], 422);
        }
    }

    /**
     * Export employees to CSV.
     * Note: Managers cannot export salary information or employee codes.
     */
    public function export(Request $request)
    {
        $query = Employee::with(['department', 'designation']);

        // Apply same filters as index
        if ($request->filled('name')) {
            $term = '%' . $request->input('name') . '%';
            $query->where(function ($q) use ($term) {
                $q->where('first_name', 'like', $term)
                    ->orWhere('middle_name', 'like', $term)
                    ->orWhere('last_name', 'like', $term)
                    ->orWhere('email', 'like', $term);
            });
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('designation_id')) {
            $query->where('designation_id', $request->designation_id);
        }

        $employees = $query->orderBy('first_name')->get();

        $filename = 'employees_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        $callback = function() use ($employees) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for proper Excel encoding
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // CSV headers (excluding employee_code and salary information for managers)
            fputcsv($file, [
                'First Name',
                'Middle Name', 
                'Last Name',
                'Email',
                'Contact',
                'Emergency Contact',
                'Gender',
                'Date of Birth',
                'Date of Joining',
                'Marital Status',
                'Address',
                'ZIP Code',
                'Pay Scale',
                'Work Location',
                'Department',
                'Designation',
                'Created At'
            ]);

            // Data rows (excluding employee_code and salary information for managers)
            foreach ($employees as $employee) {
                fputcsv($file, [
                    $employee->first_name ?: '',
                    $employee->middle_name ?: '',
                    $employee->last_name ?: '',
                    $employee->email ?: '',
                    $employee->contact ?: '',
                    $employee->emergency_contact ?: '',
                    $employee->gender ? ucfirst($employee->gender) : '',
                    $employee->dob ? $employee->dob->format('Y-m-d') : '',
                    $employee->doj ? $employee->doj->format('Y-m-d') : '',
                    $employee->marital_status ? ucfirst($employee->marital_status) : '',
                    $employee->address ?: '',
                    $employee->zip ?: '',
                    $employee->pay_scale ?: '',
                    $employee->work_location ?: '',
                    $employee->department ? $employee->department->name : '',
                    $employee->designation ? $employee->designation->name : '',
                    $employee->created_at ? $employee->created_at->format('Y-m-d H:i:s') : '',
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}