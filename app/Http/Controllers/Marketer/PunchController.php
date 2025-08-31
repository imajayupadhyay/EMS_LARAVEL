<?php

namespace App\Http\Controllers\Marketer;

use App\Http\Controllers\Controller;
use App\Models\MarketerPunch;
use App\Models\MarketerLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PunchController extends Controller
{
    // ✅ Punch In
    public function punchIn(Request $request)
    {
        $marketer = Auth::guard('marketer')->user();
        if (!$marketer) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }

        // ✅ Check base location
        $lastLocation = $marketer->locations()->latest('recorded_at')->first();
        if (!$lastLocation) {
            return response()->json(['error' => 'No base location assigned'], 422);
        }

        $distance = $this->calculateDistance(
            $lastLocation->latitude,
            $lastLocation->longitude,
            $request->latitude,
            $request->longitude
        );

        if ($distance > 300) {
            return response()->json([
                'error'    => 'Outside allowed range',
                'distance' => round($distance) . " meters",
                'allowed'  => "300 meters"
            ], 403);
        }

        // ✅ Prevent duplicate punch-in
        $activePunch = MarketerPunch::where('marketer_id', $marketer->id)
            ->where('status', 'in')
            ->first();
        if ($activePunch) {
            return response()->json(['error' => 'Already punched in'], 409);
        }

        $punch = MarketerPunch::create([
            'marketer_id'   => $marketer->id,
            'punched_in_at' => now(),
            'status'        => 'in',
        ]);

        return response()->json(['message' => 'Punched in successfully', 'punch' => $punch]);
    }

    // ✅ Punch Out
    public function punchOut()
    {
        $marketer = Auth::guard('marketer')->user();
        if (!$marketer) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }

        $lastPunch = MarketerPunch::where('marketer_id', $marketer->id)
            ->where('status', 'in')
            ->latest()
            ->first();

        if (!$lastPunch) {
            return response()->json(['error' => 'No active punch found'], 404);
        }

        $lastPunch->update([
            'punched_out_at' => now(),
            'status'         => 'out',
        ]);

        return response()->json(['message' => 'Punched out successfully']);
    }

    // ✅ Punch Status
    public function status()
    {
        $marketer = auth('marketer')->user();
        if (!$marketer) {
            return response()->json([
                'punchedIn'   => false,
                'punchInTime' => null,
                'error'       => 'Not authenticated'
            ], 401);
        }

        $lastPunch = MarketerPunch::where('marketer_id', $marketer->id)
            ->latest()
            ->first();

        return response()->json([
            'punchedIn'   => $lastPunch && $lastPunch->status === 'in',
            'punchInTime' => $lastPunch && $lastPunch->status === 'in'
                ? $lastPunch->punched_in_at->toDateTimeString()
                : null,
        ]);
    }

    // ✅ Total worked today
    public function todayWorked()
    {
        $marketer = auth('marketer')->user();
        if (!$marketer) {
            return response()->json(['total' => "00:00:00"], 401);
        }

        $punches = MarketerPunch::where('marketer_id', $marketer->id)
            ->whereDate('punched_in_at', today())
            ->get();

        $totalSeconds = 0;
        foreach ($punches as $p) {
            $in = $p->punched_in_at;
            $out = $p->punched_out_at ?? now();
            $totalSeconds += $out->diffInSeconds($in);
        }

        $hours = floor($totalSeconds / 3600);
        $minutes = floor(($totalSeconds % 3600) / 60);
        $seconds = $totalSeconds % 60;

        return response()->json([
            'total' => sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds),
        ]);
    }

    // ✅ Location update (every 30s)
    public function updateLocation(Request $request)
    {
        $marketer = Auth::guard('marketer')->user();
        if (!$marketer) {
            return response()->json(['error' => 'Not authenticated'], 401);
        }

        $lastLocation = $marketer->locations()->latest('recorded_at')->first();
        if (!$lastLocation) {
            return response()->json(['message' => 'No base location set']);
        }

        $distance = $this->calculateDistance(
            $lastLocation->latitude,
            $lastLocation->longitude,
            $request->latitude,
            $request->longitude
        );

        if ($distance > 300) {
            $lastPunch = MarketerPunch::where('marketer_id', $marketer->id)
                ->where('status', 'in')
                ->latest()
                ->first();

            if ($lastPunch) {
                $lastPunch->update([
                    'punched_out_at' => now(),
                    'status'         => 'out',
                ]);
            }

            return response()->json([
                'message'  => 'Moved outside allowed range. Auto punch-out.',
                'distance' => round($distance) . " meters"
            ]);
        }

        // log new location
        MarketerLocation::create([
            'marketer_id' => $marketer->id,
            'latitude'    => $request->latitude,
            'longitude'   => $request->longitude,
            'recorded_at' => now(),
        ]);

        return response()->json(['message' => 'Location updated']);
    }

    // ✅ Helper
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // meters
        $lat1 = deg2rad($lat1);
        $lat2 = deg2rad($lat2);
        $deltaLat = $lat2 - $lat1;
        $deltaLon = deg2rad($lon2 - $lon1);

        $a = sin($deltaLat / 2) ** 2 +
             cos($lat1) * cos($lat2) *
             sin($deltaLon / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }
}
