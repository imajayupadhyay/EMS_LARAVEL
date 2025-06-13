<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmployeeManageController extends Controller
{
    public function index(Request $request)
    {
        $employees = Employee::with(['department', 'designation'])
            ->when($request->name, fn($q) => $q->where('name', 'like', '%' . $request->name . '%'))
            ->when($request->department_id, fn($q) => $q->where('department_id', $request->department_id))
            ->when($request->designation_id, fn($q) => $q->where('designation_id', $request->designation_id))
            ->latest()
            ->get();

        return Inertia::render('Admin/Employees/Index', [
            'employees' => $employees,
            'departments' => Department::all(),
            'designations' => Designation::all(),
        ]);
    }

    public function update(Request $request, Employee $employee)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email,' . $employee->id,
            'department_id' => 'required|exists:departments,id',
            'designation_id' => 'required|exists:designations,id',
        ]);

        $employee->update($validated);

        return back()->with('success', 'Employee updated successfully.');
    }

    public function destroy(Employee $employee)
    {
        $employee->delete();

        return back()->with('success', 'Employee deleted successfully.');
    }
}
