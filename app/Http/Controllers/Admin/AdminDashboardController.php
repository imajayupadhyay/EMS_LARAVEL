<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class AdminDashboardController extends Controller
{
    /**
     * Render the admin dashboard page (Inertia).
     * Minimal: only passes authenticated admin info to the page.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        return Inertia::render('Admin/Dashboard', [
            'admin' => [
                'id' => $user->id ?? null,
                'name' => $user->name ?? 'Admin',
            ],
        ]);
    }
}
