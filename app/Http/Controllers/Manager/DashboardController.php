<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    // No constructor middleware needed if routes already use ->middleware('auth')

    /**
     * Show manager dashboard.
     */
    public function index(Request $request)
    {
        // Example props to pass — add real stats/queries if you want
        $data = [
            'stats' => [
                'employees' => 42,
                'pending_leaves' => 3,
                'today_attendance' => 28,
            ],
            // you can pass user via $request->user() if necessary
        ];

        return Inertia::render('Manager/Dashboard', $data);
    }
}
