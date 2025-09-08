<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Punch;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

// NEW imports
use App\Models\LeaveType;
use App\Models\LeaveAssignment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class PunchController extends Controller
{
    /**
     * Show punches for the employee for a date (defaults to today).
     */
    public function index(Request $request)
    {
        $employee = Auth::user();
        $date = $request->input('date') ?? now()->toDateString();

        $punches = Punch::where('employee_id', $employee->id)
            ->whereDate('created_at', $date)
            ->orderBy('created_at')
            ->get();

        // Fetch allowed locations in a simple array shape for frontend
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

    /**
     * Store a punch (punch in or punch out).
     * - Validates location presence
     * - Verifies proximity to allowed locations
     * - Creates a new Punch record (punch-in) or updates latest (punch-out)
     * - If punch-in is the FIRST punch of the day and it's Sunday (or force_sunday), auto-credit 'sunday' leave once
     */
    public function store(Request $request)
    {
        $employee = Auth::user();

        // Validate request (location must be provided as "lat,lng" or null)
        $validator = Validator::make($request->all(), [
            'location' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return back()->with('error', 'Invalid input.');
        }

        $locationInput = $request->input('location');
        $now = now();

        if (!$locationInput) {
            return back()->with('error', 'Location required.');
        }

        // Validate location format and parse lat,lng
        if (! str_contains($locationInput, ',')) {
            return back()->with('error', 'Invalid location format.');
        }

        [$lat, $lng] = explode(',', $locationInput);

        // ✅ Check against all saved office locations (distance in km threshold = 0.3)
        $allowedLocations = Location::all();
        $isWithinRange = false;

        foreach ($allowedLocations as $loc) {
            $distance = $this->calculateDistance((float)$lat, (float)$lng, (float)$loc->latitude, (float)$loc->longitude);
            if ($distance <= 0.3) { // 300 meters = 0.3 km
                $isWithinRange = true;
                break;
            }
        }

        if (!$isWithinRange) {
            return back()->with('error', 'You are not within 300m of any allowed office location.');
        }

        // Count punches created for today BEFORE we create the new punch.
        // This is used to ensure we auto-credit Sunday leave only once per day (first punch).
        $todaysPunchCount = Punch::where('employee_id', $employee->id)
            ->whereDate('created_at', now())
            ->count();

        // Get latest punch record for today to decide in/out behavior
        $latest = Punch::where('employee_id', $employee->id)
            ->whereDate('created_at', now())
            ->latest()
            ->first();

        // If there's no latest or it's punched out already -> create a new Punch (PUNCH IN)
        if (!$latest || $latest->punched_out_at) {
            $punch = Punch::create([
                'employee_id'    => $employee->id,
                'punched_in_at'  => $now,
                'location'       => $locationInput,
            ]);

            // --- NEW: Run Sunday-auto-credit only when this is the FIRST punch of the day ---
            try {
                DB::transaction(function () use ($punch, $now, $todaysPunchCount) {
                    // If employee already had a punch earlier today, skip auto-credit to prevent duplicates.
                    if ($todaysPunchCount > 0) {
                        Log::info('Skipping sunday auto-credit (not first punch of day)', [
                            'employee_id' => $punch->employee_id,
                            'todaysPunchCount' => $todaysPunchCount,
                            'timestamp' => $now->toDateTimeString(),
                        ]);
                        return;
                    }

                    // Determine whether to treat this as a Sunday punch:
                    $isSundayPunch = $now->isSunday();
                    if (! $isSundayPunch) {
                        return;
                    }

                    // Find leave type 'sunday' (case-insensitive)
                    $leaveType = LeaveType::whereRaw('LOWER(name) = ?', [strtolower('sunday')])->first();

                    if (! $leaveType) {
                        Log::warning("Punch created on Sunday but leave_type 'sunday' not found", [
                            'employee_id' => $punch->employee_id,
                            'timestamp' => $now->toDateTimeString(),
                        ]);
                        return;
                    }

                    // Find or create assignment and increment safely (use increment to avoid race conditions)
                    $assignment = LeaveAssignment::firstOrCreate(
                        [
                            'employee_id'   => $punch->employee_id,
                            'leave_type_id' => $leaveType->id,
                        ],
                        [
                            'total_assigned' => 0,
                            'balance'        => 0,
                        ]
                    );

                    // Use atomic increments
                    $assignment->increment('total_assigned', 1);
                    $assignment->increment('balance', 1);

                    // Notify frontend by session flash (Inertia will carry this)
                    session()->flash('sunday_incremented', true);

                    Log::info('Sunday leave auto-credited', [
                        'employee_id' => $punch->employee_id,
                        'leave_type_id' => $leaveType->id,
                        'assignment_id' => $assignment->id,
                        'timestamp' => now()->toDateTimeString(),
                    ]);
                });
            } catch (\Throwable $e) {
                // Log and continue - do not fail the punch creation because leave credit failed
                Log::error("Error while auto-crediting sunday leave for punch id {$punch->id}: " . $e->getMessage(), [
                    'exception' => $e,
                ]);
            }

            return redirect()->route('employee.punches.index')->with('success', 'Punched In successfully!');
        }

        // ELSE -> We have an open punch for today, so we will PUNCH OUT (update the latest)
        $latest->update(['punched_out_at' => $now]);
        return redirect()->route('employee.punches.index')->with('success', 'Punched Out successfully!');
    }

    /**
     * Returns true if the employee is currently punched in for today.
     */
    private function isEmployeeCurrentlyPunchedIn($employeeId)
    {
        $latest = Punch::where('employee_id', $employeeId)
            ->whereDate('created_at', now())
            ->latest()
            ->first();

        return $latest && $latest->punched_in_at && !$latest->punched_out_at;
    }

    /**
     * Haversine distance (in km) between two lat/lng points.
     */
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
