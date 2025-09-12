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

    // Validate request: location must be a string "lat,lng" (numbers, optional spaces)
    $validator = Validator::make($request->all(), [
        'location' => ['required', 'string', 'regex:/^\s*-?\d+(\.\d+)?\s*,\s*-?\d+(\.\d+)?\s*$/'],
    ]);

    if ($validator->fails()) {
        Log::warning('Punch validation failed', [
            'employee_id' => $employee?->id,
            'errors' => $validator->errors()->all(),
            'payload' => $request->all(),
        ]);

        // Return JSON for XHR or redirect for standard request
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => false, 'message' => 'Invalid location format. Expected "lat,lng".', 'errors' => $validator->errors()], 422);
        }
        return back()->with('error', 'Invalid location format.');
    }

    $locationInput = trim($request->input('location'));
    $now = now();

    // parse lat,lng
    [$lat, $lng] = array_map('trim', explode(',', $locationInput));
    $lat = (float) $lat;
    $lng = (float) $lng;

    // check allowed locations
    $allowedLocations = Location::all();
    $isWithinRange = false;
    foreach ($allowedLocations as $loc) {
        $distance = $this->calculateDistance($lat, $lng, (float)$loc->latitude, (float)$loc->longitude);
        if ($distance <= 0.3) {
            $isWithinRange = true;
            break;
        }
    }

    if (! $isWithinRange) {
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => false, 'message' => 'You are not within 300m of any allowed office location.'], 403);
        }
        return back()->with('error', 'You are not within 300m of any allowed office location.');
    }

    try {
        // use punched_in_at/date semantics (consistent)
        $today = $now->toDateString();

        $todaysPunchCount = Punch::where('employee_id', $employee->id)
            ->whereDate('punched_in_at', $today)
            ->count();

        $latest = Punch::where('employee_id', $employee->id)
            ->whereDate('punched_in_at', $today)
            ->latest('punched_in_at')
            ->first();

        if (! $latest || $latest->punched_out_at) {
            $punch = Punch::create([
                'employee_id'   => $employee->id,
                'punched_in_at' => $now,
                'location'      => $locationInput,
            ]);

            // Sunday auto-credit (only on first punch of day)
            if ($todaysPunchCount === 0 && $now->isSunday()) {
                try {
                    DB::transaction(function () use ($punch) {
                        $leaveType = LeaveType::whereRaw('LOWER(name) = ?', [strtolower('sunday')])->first();
                        if (! $leaveType) {
                            Log::warning('Leave type "sunday" not found', ['employee_id'=>$punch->employee_id]);
                            return;
                        }

                        $assignment = LeaveAssignment::firstOrCreate(
                            ['employee_id' => $punch->employee_id, 'leave_type_id' => $leaveType->id],
                            ['total_assigned' => 0, 'balance' => 0]
                        );

                        $assignment->increment('total_assigned', 1);
                        $assignment->increment('balance', 1);
                        session()->flash('sunday_incremented', true);
                    });
                } catch (\Throwable $e) {
                    Log::error('Sunday credit failed after punch create: '.$e->getMessage(), ['trace'=>$e->getTraceAsString()]);
                    // do not fail punch creation for leave-credit failure
                }
            }

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Punched In successfully!', 'punch' => $punch], 201);
            }
            return redirect()->route('employee.punches.index')->with('success', 'Punched In successfully!');
        }

        // PUNCH OUT -> update latest
        $latest->update(['punched_out_at' => $now]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Punched Out successfully!', 'punch' => $latest], 200);
        }
        return redirect()->route('employee.punches.index')->with('success', 'Punched Out successfully!');
    } catch (\Throwable $e) {
        Log::error('Punch store exception: '.$e->getMessage(), ['trace' => $e->getTraceAsString(), 'payload' => $request->all()]);
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => false, 'message' => 'Server error when saving punch.'], 500);
        }
        return back()->with('error', 'Failed to record punch. Please contact admin.');
    }
}


    /**
     * Returns true if the employee is currently punched in for today.
     */
    private function isEmployeeCurrentlyPunchedIn($employeeId)
    {
        $latest = Punch::where('employee_id', $employeeId)
            ->whereDate('punched_in_at', now()->toDateString())
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
