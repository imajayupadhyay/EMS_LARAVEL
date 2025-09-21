<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AutoPunchOutController;

// ... existing routes ...

// Auto Punch-Out API Routes
// These routes can be called via HTTP requests (e.g., from external systems or cron jobs)
Route::prefix('auto-punchout')->group(function () {
    // Trigger auto punch-out for previous days
    Route::post('/trigger', [AutoPunchOutController::class, 'triggerAutoPunchOut']);
    
    // Trigger end of day punch-out for today
    Route::post('/end-of-day', [AutoPunchOutController::class, 'triggerEndOfDayPunchOut']);
    
    // Get statistics about open punches
    Route::get('/stats', [AutoPunchOutController::class, 'getOpenPunchesStats']);
});