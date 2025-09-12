<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Designation;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    /**
     * Show create employee page.
     */
    public function create()
    {
        return Inertia::render('Admin/Employees/Create', [
            'departments' => Department::orderBy('name')->get(),
            'designations' => Designation::orderBy('name')->get(),
            // sensible defaults for the form
            'defaults' => [
                'salary_currency' => 'INR',
                'salary_type' => 'monthly',
            ],
        ]);
    }

    /**
     * Store a new employee.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'password' => 'required|string|min:6|confirmed', // use password_confirmation in form
            'gender' => 'required|string|max:50',
            'dob' => 'required|date',
            'doj' => 'required|date',
            'marital_status' => 'nullable|string|max:50',
            'contact' => 'required|string|max:50',
            'emergency_contact' => 'nullable|string|max:50',
            'address' => 'required|string|max:1000',
            'zip' => 'nullable|string|max:50',
            'pay_scale' => 'nullable|string|max:255',
            'work_location' => 'nullable|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'designation_id' => 'required|exists:designations,id',

            // salary fields (optional for now)
            'monthly_salary' => 'nullable|numeric|min:0',
            'salary_currency' => 'nullable|string|size:3',
            'salary_type' => 'nullable|in:monthly,daily,hourly',
        ]);

        // Hash password before creating
        $validated['password'] = Hash::make($validated['password']);

        // ensure defaults for salary fields
        if (! array_key_exists('monthly_salary', $validated) || $validated['monthly_salary'] === null) {
            $validated['monthly_salary'] = 0.00;
        }
        if (empty($validated['salary_currency'])) {
            $validated['salary_currency'] = 'INR';
        }
        if (empty($validated['salary_type'])) {
            $validated['salary_type'] = 'monthly';
        }

        // Create employee (make sure Employee::$fillable includes salary fields)
        $employee = Employee::create($validated);

        return redirect()
            ->route('admin.employees.create')
            ->with('success', "Employee ({$employee->first_name} {$employee->last_name}) registered successfully.");
    }
}
