<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Designation;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmployeeController extends Controller
{
    public function create()
    {
        return Inertia::render('Admin/Employees/Create', [
            'departments' => Department::all(),
            'designations' => Designation::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string',
            'middle_name' => 'nullable|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:employees,email',
            'password' => 'required|string|min:6',
            'gender' => 'required|string',
            'dob' => 'required|date',
            'doj' => 'required|date',
            'marital_status' => 'required|string',
            'contact' => 'required|string',
            'emergency_contact' => 'nullable|string',
            'address' => 'required|string',
            'zip' => 'required|string',
            'pay_scale' => 'required|string',
            'work_location' => 'required|string',
            'department_id' => 'required|exists:departments,id',
            'designation_id' => 'required|exists:designations,id',
        ]);

        // Mutator in model will hash password
        $validated['password'] = $request->password;

        Employee::create($validated);

        return redirect()
            ->route('admin.employees.create')
            ->with('success', 'Employee registered successfully.');
    }
}
