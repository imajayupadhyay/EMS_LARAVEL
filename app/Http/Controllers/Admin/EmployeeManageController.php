<?php

namespace App\Http\Controllers\Admin;

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
        $employees = Employee::with(['department', 'designation'])
            ->when($request->name, fn($q) => 
                $q->where('first_name', 'like', '%' . $request->name . '%')
                  ->orWhere('last_name', 'like', '%' . $request->name . '%')
            )
            ->when($request->department_id, fn($q) => $q->where('department_id', $request->department_id))
            ->when($request->designation_id, fn($q) => $q->where('designation_id', $request->designation_id))
            ->get();

        return Inertia::render('Admin/Employees/Index', [
            'employees' => $employees,
            'departments' => Department::all(),
            'designations' => Designation::all(),
            'filters' => $request->only('name', 'department_id', 'designation_id'),
        ]);
    }

    /**
     * Update the specified employee in storage.
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
            'address' => 'nullable|string|max:255',
            'zip' => 'nullable|string|max:255',
            'pay_scale' => 'nullable|string|max:255',
            'work_location' => 'nullable|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'designation_id' => 'required|exists:designations,id',
        ]);

        $employee->update($validated);

        return redirect()->back()->with('success', 'Employee updated successfully!');
    }

    /**
     * Remove the specified employee from storage.
     */
    public function destroy(Employee $employee)
    {
        $employee->delete();

        return redirect()->back()->with('success', 'Employee deleted successfully!');
    }
}
