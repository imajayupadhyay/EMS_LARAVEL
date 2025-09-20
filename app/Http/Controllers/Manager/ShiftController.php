<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Shift;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ShiftController extends Controller
{
    public function index()
    {
        return Inertia::render('Manager/Shifts/Index', [
            'shifts' => Shift::withCount('employees')->latest()->get(),
        ]);
    }
}