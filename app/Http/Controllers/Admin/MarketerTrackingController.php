<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Marketer;
use App\Models\MarketerPunch;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MarketerTrackingController extends Controller
{
    public function liveLocations()
    {
        // eager load latest location per marketer for efficiency
        $marketers = Marketer::with(['locations' => function ($q) {
            $q->latest('recorded_at')->limit(1);
        }])->get();

        $punchedIn = [];
        $punchedOut = [];

        // Attach last_punch and normalize locations to arrays; convert to simple arrays for Inertia
        $marketersPayload = $marketers->map(function ($marketer) use (&$punchedIn, &$punchedOut) {
            $lastPunch = MarketerPunch::where('marketer_id', $marketer->id)->latest()->first();

            $locations = $marketer->locations ? $marketer->locations->toArray() : [];

            // push into punched lists for the sidebar (basic info only)
            if ($lastPunch && $lastPunch->status === 'in') {
                $punchedIn[] = [
                    'id' => $marketer->id,
                    'name' => trim($marketer->first_name . ' ' . $marketer->last_name),
                    'email' => $marketer->email,
                ];
            } else {
                $punchedOut[] = [
                    'id' => $marketer->id,
                    'name' => trim($marketer->first_name . ' ' . $marketer->last_name),
                    'email' => $marketer->email,
                ];
            }

            return [
                'id' => $marketer->id,
                'first_name' => $marketer->first_name,
                'last_name' => $marketer->last_name,
                'email' => $marketer->email,
                'contact' => $marketer->contact,
                'locations' => $locations,
                // include last_punch as a simple object (or null)
                'last_punch' => $lastPunch ? $lastPunch->toArray() : null,
            ];
        });

        return Inertia::render('Admin/Marketers/LiveTracking', [
            'marketers'  => $marketersPayload,
            'punchedIn'  => $punchedIn,
            'punchedOut' => $punchedOut,
        ]);
    }

    // AJAX: latest location + last punch for an individual marketer
    public function latestLocation($id)
    {
        $marketer = Marketer::with(['locations' => function ($q) {
            $q->latest('recorded_at')->limit(1);
        }])->findOrFail($id);

        $lastPunch = MarketerPunch::where('marketer_id', $id)->latest()->first();

        $location = null;
        if ($marketer->locations && count($marketer->locations) > 0) {
            $location = $marketer->locations[0]; // latest
        }

        return response()->json([
            'success' => true,
            'data' => [
                'marketer' => [
                    'id' => $marketer->id,
                    'name' => trim($marketer->first_name . ' ' . $marketer->last_name),
                    'email' => $marketer->email,
                    'contact' => $marketer->contact,
                ],
                'last_punch' => $lastPunch ? $lastPunch->toArray() : null,
                'location' => $location ? (is_array($location) ? $location : $location->toArray()) : null,
            ],
        ]);
    }
}
