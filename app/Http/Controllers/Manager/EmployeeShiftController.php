<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Shift;
use App\Models\Department;
use App\Models\Designation;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmployeeShiftController extends Controller
{
    public function index(Request $request)
    {
        $employees = Employee::with(['department', 'designation', 'shift'])
            ->when($request->department_id, fn($q) => $q->where('department_id', $request->department_id))
            ->when($request->shift_id, fn($q) => $q->where('shift_id', $request->shift_id))
            ->orderBy('first_name')
            ->get();

        return Inertia::render('Manager/EmployeeShift/Index', [
            'employees' => $employees,
            'shifts' => Shift::orderBy('name')->get(),
            'departments' => Department::orderBy('name')->get(),
            'designations' => Designation::orderBy('name')->get(),
            'filters' => $request->only('department_id', 'shift_id'),
        ]);
    }

    public function assignShift(Request $request, Employee $employee)
    {
        $request->validate([
            'shift_id' => 'nullable|exists:shifts,id',
        ]);

        $employee->update([
            'shift_id' => $request->shift_id,
        ]);

        return redirect()->back()->with('success', 'Shift assigned successfully!');
    }
}