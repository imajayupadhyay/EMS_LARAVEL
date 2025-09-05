<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    /**
     * Return dashboard stats for admin front-end.
     * GET /admin/api/dashboard
     */
    public function stats(Request $request)
    {
        $admin = Auth::user();

        // Today (server timezone) - adjust if needed
        $today = Carbon::now()->toDateString();

        // Total employees
        $totalEmployees = DB::table('employees')->count();

        // Employees who have any punches today (present = had at least one punch today)
        $presentRows = DB::table('punches')
            ->select('employee_id', DB::raw('MAX(punched_in_at) as last_in'), DB::raw('MAX(punched_out_at) as last_out'))
            ->whereDate('punched_in_at', $today)
            ->groupBy('employee_id')
            ->get();

        $presentCount = $presentRows->count();

        // Absent = total - present
        $absentCount = max(0, $totalEmployees - $presentCount);

        // Build detailed list: join with employees table to get names and contact
        // We'll get per-present-employee: employee_id, name, last_in, last_out, duration_seconds, duration_hhmm
        $presentEmployees = [];

        foreach ($presentRows as $row) {
            $emp = DB::table('employees')->where('id', $row->employee_id)->first();

            // compute duration for the last punch row (if last_out exists)
            $lastIn = $row->last_in ? Carbon::parse($row->last_in) : null;
            $lastOut = $row->last_out ? Carbon::parse($row->last_out) : null;

            $durationSeconds = 0;
            if ($lastIn && $lastOut) {
                $durationSeconds = $lastOut->diffInSeconds($lastIn);
            } elseif ($lastIn && !$lastOut) {
                // If last_out is NULL, treat as ongoing (count up to now)
                $durationSeconds = Carbon::now()->diffInSeconds($lastIn);
            }

            $hours = floor($durationSeconds / 3600);
            $minutes = floor(($durationSeconds % 3600) / 60);
            $duration_hhmm = sprintf('%02d:%02d', $hours, $minutes);

            $presentEmployees[] = [
                'employee_id' => $row->employee_id,
                'name' => $emp ? trim(($emp->first_name ?? '') . ' ' . ($emp->last_name ?? '')) : 'Unknown',
                'email' => $emp ? ($emp->email ?? '') : '',
                'contact' => $emp ? ($emp->contact ?? '') : '',
                'last_in' => $lastIn ? $lastIn->toDateTimeString() : null,
                'last_out' => $lastOut ? $lastOut->toDateTimeString() : null,
                'duration_seconds' => $durationSeconds,
                'duration_hhmm' => $duration_hhmm,
            ];
        }

        // Optional: Sort present employees by last_in desc
        usort($presentEmployees, function($a, $b) {
            return strtotime($b['last_in'] ?? '1970-01-01') <=> strtotime($a['last_in'] ?? '1970-01-01');
        });

        return response()->json([
            'admin' => [
                'id' => $admin->id ?? null,
                'name' => $admin->name ?? 'Admin',
            ],
            'totals' => [
                'total_employees' => $totalEmployees,
                'present_today' => $presentCount,
                'absent_today' => $absentCount,
            ],
            'present_employees' => $presentEmployees,
            'date' => $today,
        ]);
    }
}
