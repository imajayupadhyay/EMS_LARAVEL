<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\Department;
use App\Models\Designation;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Http\JsonResponse;

class EmployeeManageController extends Controller
{
    /**
     * Display a listing of employees with optional filters.
     */
    public function index(Request $request)
    {
        $employees = Employee::with(['department', 'designation'])
            ->when($request->name, function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $term = '%' . $request->name . '%';
                    $sub->where('first_name', 'like', $term)
                        ->orWhere('middle_name', 'like', $term)
                        ->orWhere('last_name', 'like', $term)
                        ->orWhereRaw("concat(first_name, ' ', last_name) LIKE ?", [$term]);
                });
            })
            ->when($request->department_id, fn($q) => $q->where('department_id', $request->department_id))
            ->when($request->designation_id, fn($q) => $q->where('designation_id', $request->designation_id))
            ->orderBy('first_name')
            ->get();

        return Inertia::render('Admin/Employees/Index', [
            'employees' => $employees,
            'departments' => Department::orderBy('name')->get(),
            'designations' => Designation::orderBy('name')->get(),
            'filters' => $request->only('name', 'department_id', 'designation_id'),
        ]);
    }

    /**
     * Return employee details (AJAX JSON) — safe fields only.
     */
    /**
 * Return employee details (AJAX JSON) — safe fields only.
 */
public function show(Employee $employee): \Illuminate\Http\JsonResponse
{
    // Eager load relations required by the view
    $employee->load(['department', 'designation']);

    // helper to safely format date-like values
    $formatDate = function ($val) {
        if ($val === null) return null;
        // If it's an instance of DateTime/Carbon, format it
        if ($val instanceof \DateTimeInterface) {
            return $val->format('Y-m-d');
        }
        // If it's a numeric timestamp
        if (is_numeric($val)) {
            return \Carbon\Carbon::createFromTimestamp($val)->toDateString();
        }
        // If it's already a string in YYYY-MM-DD or similar, try to parse and normalize
        try {
            $dt = \Carbon\Carbon::parse($val);
            return $dt->toDateString();
        } catch (\Throwable $e) {
            // fallback: return original string
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
        'department' => $employee->department ? [
            'id' => $employee->department->id,
            'name' => $employee->department->name,
        ] : null,
        'designation' => $employee->designation ? [
            'id' => $employee->designation->id,
            'name' => $employee->designation->name,
        ] : null,
        'created_at' => $employee->created_at ? $employee->created_at->toDateTimeString() : null,
        'updated_at' => $employee->updated_at ? $employee->updated_at->toDateTimeString() : null,
    ];

    return response()->json([
        'success' => true,
        'data' => $data,
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
