<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeaveType;
use Illuminate\Http\Request;

class LeaveTypeController extends Controller
{
    public function index()
    {
        $leaveTypes = LeaveType::orderBy('name')->paginate(10);

        return inertia('Admin/LeaveTypes/Index', [
            'leaveTypes' => $leaveTypes
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:leave_types,name',
            'allowed_days' => 'required|integer|min:1'
        ]);

        LeaveType::create($request->only('name', 'allowed_days'));

        return back()->with('success', 'Leave type added successfully.');
    }

    public function update(Request $request, LeaveType $leaveType)
    {
        $request->validate([
            'name' => 'required|string|unique:leave_types,name,' . $leaveType->id,
            'allowed_days' => 'required|integer|min:1'
        ]);

        $leaveType->update($request->only('name', 'allowed_days'));

        return back()->with('success', 'Leave type updated successfully.');
    }

    public function destroy(LeaveType $leaveType)
    {
        $leaveType->delete();

        return back()->with('success', 'Leave type deleted successfully.');
    }
}
