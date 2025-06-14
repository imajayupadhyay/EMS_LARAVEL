<?php
namespace App\Exports;

use App\Models\Punch;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class AttendanceExport implements FromView
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        $query = Punch::with('user.designation')
            ->when($this->request->employee_id, fn ($q) => $q->where('user_id', $this->request->employee_id))
            ->when($this->request->date, fn ($q) => $q->whereDate('punched_in_at', $this->request->date))
            ->when($this->request->month, function ($q) {
                $month = Carbon::parse($this->request->month)->month;
                $year = Carbon::parse($this->request->month)->year;
                $q->whereMonth('punched_in_at', $month)->whereYear('punched_in_at', $year);
            });

        $punches = $query->get();

        $attendance = $punches->groupBy(function ($punch) {
            return $punch->user_id . '-' . Carbon::parse($punch->punched_in_at)->format('Y-m-d');
        })->map(function ($group) {
            $first = $group->first();
            $total = 0;

            $pairs = $group->chunk(2);
            foreach ($pairs as $pair) {
                if (isset($pair[0]) && isset($pair[1])) {
                    $in = Carbon::parse($pair[0]->punched_in_at);
                    $out = Carbon::parse($pair[1]->punched_out_at ?? now());
                    $total += $in->diffInSeconds($out);
                }
            }

            return [
                'user' => $first->user->name,
                'date' => Carbon::parse($first->punched_in_at)->format('Y-m-d'),
                'hours' => gmdate('H:i:s', $total),
            ];
        })->values();

        $totalWorkingDays = $punches->groupBy(function ($punch) {
            return Carbon::parse($punch->punched_in_at)->format('Y-m-d');
        })->count();

        return view('exports.attendance', [
            'attendance' => $attendance,
            'totalWorkingDays' => $totalWorkingDays
        ]);
    }
}
