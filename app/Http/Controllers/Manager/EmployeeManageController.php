<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Designation;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmployeeManageController extends Controller
{
    /**
     * Display a listing of employees with optional filters.
     */
    public function index(Request $request)
    {
        $query = Employee::with(['department', 'designation']);

        // Search by name (first/middle/last or combined)
        if ($request->filled('name')) {
            $term = '%' . $request->input('name') . '%';
            $query->where(function ($q) use ($term) {
                $q->where('first_name', 'like', $term)
                    ->orWhere('middle_name', 'like', $term)
                    ->orWhere('last_name', 'like', $term)
                    ->orWhereRaw("concat(first_name, ' ', last_name) LIKE ?", [$term]);
            });
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('designation_id')) {
            $query->where('designation_id', $request->designation_id);
        }

        // safe ordering and pagination (15 per page)
        $employees = $query->orderBy('first_name')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Manager/Employees/Index', [
            'employees' => $employees,
            'departments' => Department::orderBy('name')->get(['id', 'name']),
            'designations' => Designation::orderBy('name')->get(['id', 'name']),
            'filters' => $request->only('name', 'department_id', 'designation_id'),
        ]);
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
            // intentionally excluded: monthly_salary, salary_currency, salary_type, payroll fields

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
     * Update the specified employee in storage.
     * (Optional: manager may or may not have permission. Keep same validation as admin if needed.)
     */
    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'gender' => 'nullable|string|max:255',
            'dob' => 'nullable|date',
            'doj' => 'nullable|date',
            'marital_status' => 'nullable|string|max:255',
            'contact' => 'required|string|max:255',
            'emergency_contact' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:1000',
            'zip' => 'nullable|string|max:50',
            'pay_scale' => 'nullable|string|max:255',
            'work_location' => 'nullable|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'designation_id' => 'required|exists:designations,id',
            // salary fields intentionally omitted for manager updates
        ]);

        $employee->update($validated);

        return redirect()->route('manager.employees.index')->with('success', 'Employee updated successfully!');
    }

    /**
     * Remove the specified employee from storage.
     * (If managers should not delete, change to abort(403).)
     */
    public function destroy(Employee $employee)
    {
        // if you don't want manager to delete, uncomment:
        // abort(403, 'Unauthorized');

        $employee->delete();

        return redirect()->route('manager.employees.index')->with('success', 'Employee deleted successfully!');
    }
}
