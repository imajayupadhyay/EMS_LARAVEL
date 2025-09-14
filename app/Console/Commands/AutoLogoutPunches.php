<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\Punch;
use App\Models\Location;

class AutoLogoutPunches extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auto:logout-punches
                            {--threshold_km=0.3 : Distance threshold in km (default 0.3 = 300m)}
                            {--recency_minutes=60 : Only consider last-known location if recorded within this many minutes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto-logout employees who have an open punch and whose last-known location is outside allowed locations';

    public function handle()
    {
        $thresholdKm = (float) $this->option('threshold_km');
        $recencyMinutes = (int) $this->option('recency_minutes');

        $this->info("Starting auto-logout (threshold={$thresholdKm} km, recency={$recencyMinutes} min)...");

        // Preload allowed locations
        $allowedLocations = Location::all()->map(function ($loc) {
            return [
                'id' => $loc->id,
                'name' => $loc->name,
                'lat' => (float) $loc->latitude,
                'lng' => (float) $loc->longitude,
            ];
        })->toArray();

        if (empty($allowedLocations)) {
            $this->warn('No allowed locations found in locations table. Aborting.');
            return 0;
        }

        // Process open punches in chunks
        Punch::whereNull('punched_out_at')
            ->orderBy('id')
            ->chunkById(200, function ($punches) use ($allowedLocations, $thresholdKm, $recencyMinutes) {
                foreach ($punches as $openPunch) {
                    $this->processOpenPunch($openPunch, $allowedLocations, $thresholdKm, $recencyMinutes);
                }
            });

        $this->info('Auto-logout pass finished.');
        return 0;
    }

    /**
     * Process single open punch row.
     */
    protected function processOpenPunch(Punch $openPunch, array $allowedLocations, float $thresholdKm, int $recencyMinutes)
    {
        try {
            // Get last-known location for this employee (most recent punch with a location)
            $lastLocationPunch = Punch::where('employee_id', $openPunch->employee_id)
                ->whereNotNull('location')
                ->orderBy('created_at', 'desc')
                ->first();

            if (! $lastLocationPunch || ! $lastLocationPunch->location) {
                Log::info('Auto-logout skipped: no last-known location', ['employee_id' => $openPunch->employee_id, 'punch_id' => $openPunch->id]);
                return;
            }

            // Check recency
            $recordedAt = $lastLocationPunch->created_at ? Carbon::parse($lastLocationPunch->created_at) : null;
            if (! $recordedAt || $recordedAt->diffInMinutes(now()) > $recencyMinutes) {
                // Location too old — skip auto-logout to avoid wrong decisions
                Log::info('Auto-logout skipped: last location too old', [
                    'employee_id' => $openPunch->employee_id,
                    'punch_id' => $openPunch->id,
                    'last_location_created_at' => $recordedAt?->toDateTimeString(),
                ]);
                return;
            }

            // parse "lat,lng" string (controller uses "lat,lng")
            [$lat, $lng] = array_map('trim', explode(',', $lastLocationPunch->location));
            $lat = (float) $lat;
            $lng = (float) $lng;

            // Is inside any allowed location?
            $isWithin = false;
            $closest = null;
            foreach ($allowedLocations as $loc) {
                $distance = $this->calculateDistance($lat, $lng, $loc['lat'], $loc['lng']); // km
                if ($closest === null || $distance < $closest['distance']) {
                    $closest = ['loc' => $loc, 'distance' => $distance];
                }
                if ($distance <= $thresholdKm) {
                    $isWithin = true;
                    break;
                }
            }

            if ($isWithin) {
                // Employee still within permitted geofence
                Log::info('Auto-logout not needed: employee in geofence', [
                    'employee_id' => $openPunch->employee_id,
                    'punch_id' => $openPunch->id,
                    'distance_km' => $closest['distance'] ?? null,
                    'location' => $lastLocationPunch->location,
                ]);
                return;
            }

            // Outside geofence -> perform auto-logout in transaction, but re-check punch is still open
            DB::transaction(function () use ($openPunch, $lastLocationPunch, $closest) {
                // Reload to avoid race
                $fresh = Punch::find($openPunch->id);
                if (! $fresh || $fresh->punched_out_at) {
                    // Already closed by someone else
                    Log::info('Auto-logout aborted: punch already closed by another process', ['punch_id' => $openPunch->id]);
                    return;
                }

                $now = now();
                $fresh->punched_out_at = $now;
                $fresh->save();

                // Create an admin notification (audit trail)
                $employeeName = $fresh->employee?->first_name ?? DB::table('employees')->where('id', $fresh->employee_id)->value('first_name') ?? 'Employee';
                $message = sprintf(
                    'Auto-logout: %s (employee_id=%d) auto-logged out at %s. Last location: %s (recorded_at: %s). Closest allowed location: %s (distance: %.3f km).',
                    $employeeName,
                    $fresh->employee_id,
                    $now->toDateTimeString(),
                    $lastLocationPunch->location,
                    optional($lastLocationPunch->created_at)->toDateTimeString(),
                    optional($closest['loc'])['name'] ?? 'N/A',
                    $closest['distance'] ?? 0
                );

                DB::table('admin_notifications')->insert([
                    'title' => 'Auto Logout',
                    'message' => $message,
                    'body' => null,
                    'is_read' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                Log::info('Auto-logout performed', [
                    'punch_id' => $fresh->id,
                    'employee_id' => $fresh->employee_id,
                    'last_location' => $lastLocationPunch->location,
                    'closest_location' => $closest['loc'] ?? null,
                    'distance_km' => $closest['distance'] ?? null
                ]);
            });

        } catch (\Throwable $e) {
            Log::error('Error processing auto-logout for punch', [
                'punch_id' => $openPunch->id,
                'employee_id' => $openPunch->employee_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    /**
     * Haversine distance in kilometers.
     */
    protected function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) ** 2 +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
