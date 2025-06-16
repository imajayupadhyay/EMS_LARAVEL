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
        $employee = Auth::user(); // Assuming your auth is employee-based
        $date = $request->input('date') ?? now()->toDateString();

        $punches = Punch::where('employee_id', $employee->id)
            ->whereDate('created_at', $date)
            ->orderBy('created_at')
            ->get();

        $location = Location::first();

        return inertia('Employee/Punches/Index', [
            'punches' => $punches,
            'allowedLocation' => $location ? [
                'lat' => $location->latitude,
                'lng' => $location->longitude
            ] : null,
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
        $allowed = Location::first();

        if (!$allowed) {
            return back()->with('error', 'Allowed location not set.');
        }

        $distance = $this->calculateDistance($lat, $lng, $allowed->latitude, $allowed->longitude);
        if ($distance > 0.5) {
            return back()->with('error', 'Not in allowed location.');
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
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) ** 2 +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }
}
