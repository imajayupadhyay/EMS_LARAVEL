<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\AutoPunchOutService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class AutoPunchOutMiddleware
{
    protected $autoPunchOutService;

    public function __construct(AutoPunchOutService $autoPunchOutService)
    {
        $this->autoPunchOutService = $autoPunchOutService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Use cache to prevent checking on every request
        // Check once per hour
        $cacheKey = 'auto_punchout_last_check';
        $lastCheck = Cache::get($cacheKey);
        
        if (!$lastCheck || now()->diffInMinutes($lastCheck) >= 60) {
            try {
                // Process any previous days' open punches
                if ($this->autoPunchOutService->hasOpenPunches()) {
                    $this->autoPunchOutService->processAutoPunchOuts();
                }
                
                // Update cache
                Cache::put($cacheKey, now(), now()->addHour());
                
            } catch (\Exception $e) {
                Log::error('Auto punch-out middleware error', [
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $next($request);
    }
}