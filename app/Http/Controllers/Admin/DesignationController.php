<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Designation;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DesignationController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Designations/DesignationList', [
            'designations' => Designation::with('department')->latest()->get(),
            'departments' => Department::all(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:designations,name',
            'department_id' => 'required|exists:departments,id',
        ]);

        Designation::create($request->only('name', 'department_id'));

        return redirect()->back()->with('success', 'Designation added.');
    }

    public function update(Request $request, Designation $designation)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:designations,name,' . $designation->id,
            'department_id' => 'required|exists:departments,id',
        ]);

        $designation->update($request->only('name', 'department_id'));

        return redirect()->back()->with('success', 'Designation updated.');
    }

    public function destroy(Designation $designation)
    {
        $designation->delete();

        return redirect()->back()->with('success', 'Designation deleted.');
    }
}
