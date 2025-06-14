<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HolidayController extends Controller
{
    public function index(Request $request)
    {
        $holidays = Holiday::query()
            ->when($request->type, fn($q) => $q->where('type', $request->type))
            ->when($request->date, fn($q) => $q->where('date', $request->date))
            ->orderBy('date')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Admin/Holidays/Index', [
            'holidays' => $holidays,
            'filters' => $request->only(['type', 'date']),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'date' => 'required|date',
            'type' => 'required|in:public,restricted'
        ]);

        Holiday::create($request->all());

        return back()->with('success', 'Holiday added successfully.');
    }

    public function update(Request $request, Holiday $holiday)
    {
        $request->validate([
            'name' => 'required|string',
            'date' => 'required|date',
            'type' => 'required|in:public,restricted'
        ]);

        $holiday->update($request->all());

        return back()->with('success', 'Holiday updated successfully.');
    }

    public function destroy(Holiday $holiday)
    {
        $holiday->delete();

        return back()->with('success', 'Holiday deleted successfully.');
    }
}
