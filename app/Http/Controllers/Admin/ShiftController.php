<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ShiftController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Shifts/Index', [
            'shifts' => Shift::latest()->get(),
            'flash' => session('success'),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'time_from' => 'required|date_format:H:i',
            'time_to' => 'required|date_format:H:i|after:time_from',
        ]);

        Shift::create($request->only('name', 'time_from', 'time_to'));

        return redirect()->route('admin.shifts.index')->with('success', 'Shift added successfully.');
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:shifts,id',
            'name' => 'required|string|max:255',
            'time_from' => 'required|date_format:H:i',
            'time_to' => 'required|date_format:H:i|after:time_from',
        ]);

        $shift = Shift::findOrFail($request->id);
        $shift->update($request->only('name', 'time_from', 'time_to'));

        return redirect()->route('admin.shifts.index')->with('success', 'Shift updated successfully.');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:shifts,id'
        ]);

        Shift::destroy($request->id);

        return redirect()->route('admin.shifts.index')->with('success', 'Shift deleted successfully.');
    }
}