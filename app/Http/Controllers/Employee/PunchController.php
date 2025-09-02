<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Punch;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PunchController extends Controller
{
    public function index(Request $request)
    {
        $employee = Auth::user();
        $date = $request->input('date') ?? now()->toDateString();

        $punches = Punch::where('employee_id', $employee->id)
            ->whereDate('created_at', $date)
            ->orderBy('created_at')
            ->get();

        // ✅ Fetch ALL locations
        $locations = Location::all()->map(fn($loc) => [
            'lat' => $loc->latitude,
            'lng' => $loc->longitude,
            'name' => $loc->name,
        ]);

        return inertia('Employee/Punches/Index', [
            'punches' => $punches,
            'allowedLocations' => $locations,
            'isPunchedIn' => $this->isEmployeeCurrentlyPunchedIn($employee->id),
            'date' => $date
        ]);
    }

    public function store(Request $request)
    {
        $employee = Auth::user();
        $locationInput = $request->input('location');
        $now = now();

        if (!$locationInput) {
            return back()->with('error', 'Location required.');
        }

        [$lat, $lng] = explode(',', $locationInput);

        // ✅ Check against all saved locations
        $allowedLocations = Location::all();
        $isWithinRange = false;

        foreach ($allowedLocations as $loc) {
            $distance = $this->calculateDistance($lat, $lng, $loc->latitude, $loc->longitude);
            if ($distance <= 0.3) { // 300 meters = 0.3 km
                $isWithinRange = true;
                break;
            }
        }

        if (!$isWithinRange) {
            return back()->with('error', 'You are not within 300m of any allowed office location.');
        }

        $latest = Punch::where('employee_id', $employee->id)
            ->whereDate('created_at', now())
            ->latest()
            ->first();

        if (!$latest || $latest->punched_out_at) {
            Punch::create([
                'employee_id' => $employee->id,
                'punched_in_at' => $now,
                'location' => $locationInput,
            ]);
            return redirect()->route('employee.punches.index')->with('success', 'Punched In successfully!');
        } else {
            $latest->update(['punched_out_at' => $now]);
            return redirect()->route('employee.punches.index')->with('success', 'Punched Out successfully!');
        }
    }

    private function isEmployeeCurrentlyPunchedIn($employeeId)
    {
        $latest = Punch::where('employee_id', $employeeId)
            ->whereDate('created_at', now())
            ->latest()
            ->first();

        return $latest && $latest->punched_in_at && !$latest->punched_out_at;
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // in km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) ** 2 +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c; // distance in km
    }
}
