<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AutoPunchOutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AutoPunchOutController extends Controller
{
    protected $autoPunchOutService;

    public function __construct(AutoPunchOutService $autoPunchOutService)
    {
        $this->autoPunchOutService = $autoPunchOutService;
    }

    /**
     * Trigger auto punch-out for previous days' open punches
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function triggerAutoPunchOut(Request $request)
    {
        try {
            // Optional: Add authentication/authorization check here
            // You might want to protect this endpoint with a secret token
            
            $secretToken = $request->header('X-Auto-Punchout-Token') ?? $request->input('token');
            $expectedToken = config('app.auto_punchout_token', 'your-secret-token-here');
            
            if ($secretToken !== $expectedToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }

            $results = $this->autoPunchOutService->processAutoPunchOuts();

            return response()->json([
                'success' => true,
                'message' => 'Auto punch-out process completed',
                'data' => $results,
            ]);

        } catch (\Exception $e) {
            Log::error('API auto punch-out failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Auto punch-out process failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Trigger end of day auto punch-out for today's open punches
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function triggerEndOfDayPunchOut(Request $request)
    {
        try {
            $secretToken = $request->header('X-Auto-Punchout-Token') ?? $request->input('token');
            $expectedToken = config('app.auto_punchout_token', 'your-secret-token-here');
            
            if ($secretToken !== $expectedToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }

            $results = $this->autoPunchOutService->processEndOfDayPunchOuts();

            return response()->json([
                'success' => true,
                'message' => 'End of day auto punch-out completed',
                'data' => $results,
            ]);

        } catch (\Exception $e) {
            Log::error('API end of day auto punch-out failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'End of day auto punch-out failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get statistics about open punches
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getOpenPunchesStats(Request $request)
    {
        try {
            $secretToken = $request->header('X-Auto-Punchout-Token') ?? $request->input('token');
            $expectedToken = config('app.auto_punchout_token', 'your-secret-token-here');
            
            if ($secretToken !== $expectedToken) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized',
                ], 401);
            }

            $hasOpenPunches = $this->autoPunchOutService->hasOpenPunches();
            
            $openPunchesCount = \App\Models\Punch::whereNull('punched_out_at')
                ->whereDate('punched_in_at', '<', \Carbon\Carbon::today())
                ->count();

            $todayOpenPunchesCount = \App\Models\Punch::whereNull('punched_out_at')
                ->whereDate('punched_in_at', \Carbon\Carbon::today())
                ->count();

            return response()->json([
                'success' => true,
                'data' => [
                    'has_open_punches' => $hasOpenPunches,
                    'previous_days_open_punches' => $openPunchesCount,
                    'today_open_punches' => $todayOpenPunchesCount,
                    'total_open_punches' => $openPunchesCount + $todayOpenPunchesCount,
                ],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get stats',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}