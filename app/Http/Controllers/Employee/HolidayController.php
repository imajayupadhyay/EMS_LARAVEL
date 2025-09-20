<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

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

        // Order by date ascending (upcoming first)
        $holidays = $query->orderBy('date', 'asc')->paginate(9)->withQueryString();

        // Get distinct types for filter dropdown
        $types = Holiday::select('type')->distinct()->pluck('type');

        // Calculate statistics
        $stats = [
            'total' => Holiday::count(),
            'upcoming' => Holiday::where('date', '>=', now()->toDateString())->count(),
            'this_month' => Holiday::whereMonth('date', now()->month)
                                  ->whereYear('date', now()->year)
                                  ->count(),
            'next_holiday_days' => $this->getNextHolidayDays(),
        ];

        // Get upcoming count
        $upcomingCount = Holiday::where('date', '>=', now()->toDateString())->count();

        // Get current month for reference
        $currentMonth = now()->format('F Y');

        return Inertia::render('Employee/Holidays/Index', [
            'holidays' => $holidays,
            'filters' => $request->only(['type', 'month']),
            'types' => $types,
            'currentMonth' => $currentMonth,
            'upcomingCount' => $upcomingCount,
            'stats' => $stats,
        ]);
    }

    /**
     * Calculate days until next holiday
     */
    private function getNextHolidayDays()
    {
        $nextHoliday = Holiday::where('date', '>', now()->toDateString())
                              ->orderBy('date', 'asc')
                              ->first();

        if (!$nextHoliday) {
            return null;
        }

        $today = Carbon::now()->startOfDay();
        $holidayDate = Carbon::parse($nextHoliday->date)->startOfDay();
        
        return $today->diffInDays($holidayDate);
    }
}