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
        $marketers = Marketer::with(['locations' => function ($q) {
            $q->latest('recorded_at')->limit(1);
        }])->get();

        // Attach punch info
        foreach ($marketers as $marketer) {
            $marketer->last_punch = MarketerPunch::where('marketer_id', $marketer->id)
                ->latest()
                ->first();
        }

        return Inertia::render('Admin/Marketers/LiveTracking', [
            'marketers' => $marketers
        ]);
    }
}
