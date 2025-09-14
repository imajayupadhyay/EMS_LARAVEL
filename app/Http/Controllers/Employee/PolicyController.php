<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Policy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class PolicyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show policies visible to current employee
     */
    public function index(Request $request)
    {
        $employee = Auth::user(); // note: your employees table is separate; adjust if you use Auth user -> employee mapping
        // If Auth user is separate from employees table, get employee model:
        // $employeeModel = \App\Models\Employee::where('email', $employee->email)->first();

        // For this implementation we assume the authenticated user is an 'employee' type and you can access department_id, designation_id, id.
        // If not, adapt to fetch Employee model by user->email or mapping.

        // Fetch active policies and filter in memory (good for moderate amounts of policies)
        $policies = Policy::where('is_active', true)->orderBy('created_at','desc')->get();

        $visible = $policies->filter(function ($p) use ($employee) {
            if ($p->audience === 'all') {
                return true;
            }
            $vals = (array) ($p->audience_value ?? []);
            if (empty($vals)) {
                return false;
            }
            switch ($p->audience) {
                case 'department':
                    return in_array($employee->department_id, $vals);
                case 'designation':
                    return in_array($employee->designation_id, $vals);
                case 'employee':
                    return in_array($employee->id, $vals);
                case 'custom':
                    // custom may contain department ids or employee ids; we'll allow if any matches
                    return (in_array($employee->id, $vals) ||
                            in_array($employee->department_id, $vals) ||
                            in_array($employee->designation_id, $vals));
                default:
                    return false;
            }
        })->values();

        return Inertia::render('Employee/Policies/Index', [
            'policies' => $visible,
        ]);
    }
}
