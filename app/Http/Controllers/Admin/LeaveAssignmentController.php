<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveAssignment;
use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LeaveAssignmentController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/LeaveAssignments/Index', [
            'assignments' => LeaveAssignment::with('user', 'leaveType')->paginate(10),
            'employees' => User::select('id', 'name')->get(),
            'leaveTypes' => LeaveType::select('id', 'name')->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'leave_type_id' => 'required|exists:leave_types,id',
            'total_assigned' => 'required|integer|min:0',
            'balance' => 'required|integer|min:0'
        ]);

        LeaveAssignment::create($request->all());

        return back()->with('success', 'Leave assigned successfully!');
    }

    public function update(Request $request, LeaveAssignment $leaveAssignment)
    {
        $request->validate([
            'total_assigned' => 'required|integer|min:0',
            'balance' => 'required|integer|min:0'
        ]);

        $leaveAssignment->update($request->only('total_assigned', 'balance'));

        return back()->with('success', 'Leave assignment updated!');
    }

    public function destroy(LeaveAssignment $leaveAssignment)
    {
        $leaveAssignment->delete();

        return back()->with('success', 'Leave assignment deleted!');
    }
}

