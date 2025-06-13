<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LocationController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Locations/Index', [
            'locations' => Location::latest()->get(),
            'flash' => session('success'),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        Location::create($request->only('name', 'latitude', 'longitude'));

        return redirect()->route('admin.locations.index')->with('success', 'Location added successfully.');
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:locations,id',
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $location = Location::findOrFail($request->id);
        $location->update($request->only('name', 'latitude', 'longitude'));

        return redirect()->route('admin.locations.index')->with('success', 'Location updated successfully.');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:locations,id'
        ]);

        Location::destroy($request->id);

        return redirect()->route('admin.locations.index')->with('success', 'Location deleted successfully.');
    }
}
