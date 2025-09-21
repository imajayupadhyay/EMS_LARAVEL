<?php

namespace App\Services;

use App\Models\Punch;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AutoPunchOutService
{
    /**
     * Process auto punch-outs for employees who forgot to punch out
     * This should be called at the end of each day (11:59 PM)
     * 
     * @return array Statistics about processed punch-outs
     */
    public function processAutoPunchOuts()
    {
        $processedCount = 0;
        $errorCount = 0;
        $results = [];

        try {
            DB::beginTransaction();

            // Get all open punches from previous days (not today)
            $openPunches = Punch::whereNull('punched_out_at')
                ->whereDate('punched_in_at', '<', Carbon::today())
                ->get();

            foreach ($openPunches as $punch) {
                try {
                    // Set punch-out time to 11:59 PM of the punch-in date
                    $punchOutTime = Carbon::parse($punch->punched_in_at)
                        ->endOfDay()
                        ->setTime(23, 59, 59);

                    $punch->update([
                        'punched_out_at' => $punchOutTime,
                        'is_auto_punchout' => 1,
                    ]);

                    $processedCount++;
                    
                    $results[] = [
                        'punch_id' => $punch->id,
                        'employee_id' => $punch->employee_id,
                        'punched_in_at' => $punch->punched_in_at->toDateTimeString(),
                        'punched_out_at' => $punchOutTime->toDateTimeString(),
                        'status' => 'success',
                    ];

                    Log::info('Auto punch-out processed', [
                        'punch_id' => $punch->id,
                        'employee_id' => $punch->employee_id,
                        'punch_in' => $punch->punched_in_at,
                        'punch_out' => $punchOutTime,
                    ]);

                } catch (\Exception $e) {
                    $errorCount++;
                    Log::error('Auto punch-out failed', [
                        'punch_id' => $punch->id,
                        'error' => $e->getMessage(),
                    ]);
                    
                    $results[] = [
                        'punch_id' => $punch->id,
                        'employee_id' => $punch->employee_id ?? null,
                        'status' => 'error',
                        'message' => $e->getMessage(),
                    ];
                }
            }

            DB::commit();

            Log::info('Auto punch-out batch completed', [
                'processed' => $processedCount,
                'errors' => $errorCount,
                'total' => $openPunches->count(),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Auto punch-out batch failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }

        return [
            'total' => count($results),
            'processed' => $processedCount,
            'errors' => $errorCount,
            'details' => $results,
        ];
    }

    /**
     * Process auto punch-out for current day at end of day
     * This is meant to be called at 11:59 PM
     */
    public function processEndOfDayPunchOuts()
    {
        $processedCount = 0;
        $errorCount = 0;
        $results = [];

        try {
            DB::beginTransaction();

            // Get all open punches from today with employee info
            $todayOpenPunches = Punch::with('employee')
                ->whereNull('punched_out_at')
                ->whereDate('punched_in_at', Carbon::today())
                ->get();

            $punchOutTime = Carbon::now()->setTime(23, 59, 59);

            foreach ($todayOpenPunches as $punch) {
                try {
                    $punch->update([
                        'punched_out_at' => $punchOutTime,
                        'is_auto_punchout' => 1,
                    ]);

                    $processedCount++;
                    
                    $results[] = [
                        'punch_id' => $punch->id,
                        'employee_id' => $punch->employee_id,
                        'employee_name' => $punch->employee ? $punch->employee->first_name . ' ' . $punch->employee->last_name : 'Unknown',
                        'status' => 'success',
                    ];

                    Log::info('End of day auto punch-out', [
                        'punch_id' => $punch->id,
                        'employee_id' => $punch->employee_id,
                        'employee_name' => $punch->employee ? $punch->employee->first_name . ' ' . $punch->employee->last_name : 'Unknown',
                    ]);

                } catch (\Exception $e) {
                    $errorCount++;
                    Log::error('End of day auto punch-out failed', [
                        'punch_id' => $punch->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            DB::commit();

            // Log summary
            Log::info('End of day auto punch-out summary', [
                'date' => Carbon::today()->toDateString(),
                'processed' => $processedCount,
                'errors' => $errorCount,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('End of day auto punch-out batch failed', ['error' => $e->getMessage()]);
            throw $e;
        }

        return [
            'total' => count($results),
            'processed' => $processedCount,
            'errors' => $errorCount,
            'details' => $results,
        ];
    }

    /**
     * Check if there are any open punches that need auto punch-out
     */
    public function hasOpenPunches()
    {
        return Punch::whereNull('punched_out_at')
            ->whereDate('punched_in_at', '<', Carbon::today())
            ->exists();
    }
}