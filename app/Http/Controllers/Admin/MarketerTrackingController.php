<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Marketer;
use App\Models\MarketerPunch;
use Illuminate\Http\Request;
use Inertia\Inertia;

class MarketerTrackingController extends Controller
{
    // existing method with enhancements
    public function liveLocations()
    {
        // eager load only the latest location per marketer for efficiency
        $marketers = Marketer::with(['locations' => function ($q) {
            $q->latest('recorded_at')->limit(1);
        }])->get();

        // attach last_punch and compute simple lists
        $punchedIn = [];
        $punchedOut = [];

        foreach ($marketers as $marketer) {
            $marketer->last_punch = MarketerPunch::where('marketer_id', $marketer->id)
                ->latest()
                ->first();

            // Normalize locations to an array (may be empty)
            if (!isset($marketer->locations) || !is_array($marketer->locations)) {
                $marketer->locations = $marketer->locations ?? [];
            }

            if ($marketer->last_punch && $marketer->last_punch->status === 'in') {
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
        }

        return Inertia::render('Admin/Marketers/LiveTracking', [
            'marketers'  => $marketers,
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
                'last_punch' => $lastPunch,
                'location' => $location,
            ],
        ]);
    }
}
