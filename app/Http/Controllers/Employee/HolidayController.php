<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HolidayController extends Controller
{
    public function index(Request $request)
    {
        $query = Holiday::query();

        // ✅ Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // ✅ Filter by month
        if ($request->filled('month')) {
            $query->whereMonth('date', $request->month);
        }

        $holidays = $query->orderBy('date', 'asc')->paginate(9)->withQueryString();

        // Get distinct types for filter dropdown
        $types = Holiday::select('type')->distinct()->pluck('type');

        return Inertia::render('Employee/Holidays/Index', [
            'holidays' => $holidays,
            'filters' => $request->only(['type', 'month']),
            'types'   => $types,
        ]);
    }
}
