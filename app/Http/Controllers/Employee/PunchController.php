<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Punch;
use App\Models\Location;
use Carbon\Carbon;

class PunchController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Get date from request or default to today
        $date = $request->input('date') ?? Carbon::today()->toDateString();
        $dateObj = Carbon::parse($date);

        // Fetch punches for the date
        $punches = Punch::where('user_id', $user->id)
            ->whereDate('created_at', $dateObj)
            ->orderBy('created_at', 'asc')
            ->get();

        // Fetch the allowed punch-in location from DB
        $location = Location::first(); // you can later customize for location-per-user/department

        return inertia('Employee/Punches/Index', [
            'punches' => $punches,
            'allowedLocation' => $location
                ? ['lat' => $location->latitude, 'lng' => $location->longitude]
                : null,
            'isPunchedIn' => $this->isUserCurrentlyPunchedIn($user->id),
            'date' => $date
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $locationInput = $request->input('location');
        $date = $request->input('date') ?? Carbon::today()->toDateString();
        $now = Carbon::now();

        if (!$locationInput) {
            return back()->with('error', 'Location is required for punching.');
        }

        [$lat, $lng] = explode(',', $locationInput);

        $allowed = Location::first();

        if (!$allowed) {
            return back()->with('error', 'Allowed location not configured.');
        }

        $distance = $this->calculateDistance($lat, $lng, $allowed->latitude, $allowed->longitude);

        if ($distance > 0.5) {
            return back()->with('error', 'You are not in the allowed location to punch.');
        }

        // Get latest punch for the date
        $latest = Punch::where('user_id', $user->id)
            ->whereDate('created_at', $date)
            ->latest()
            ->first();

        if (!$latest || $latest->punched_out_at) {
            // New Punch In
            Punch::create([
                'user_id' => $user->id,
                'punched_in_at' => $now,
                'location' => $locationInput,
            ]);
            return back()->with('success', 'Punched In successfully!');
        } else {
            // Punch Out
            $latest->update([
                'punched_out_at' => $now
            ]);
            return back()->with('success', 'Punched Out successfully!');
        }
    }

    private function isUserCurrentlyPunchedIn($userId)
    {
        $latest = Punch::where('user_id', $userId)
            ->whereDate('created_at', Carbon::today())
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
        return $earthRadius * $c; // distance in km
    }
}
