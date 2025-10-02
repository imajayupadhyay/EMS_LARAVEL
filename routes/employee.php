<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Employee\PunchController;
use App\Http\Controllers\Employee\TaskController;
use App\Http\Controllers\Employee\LeaveApplicationController;
use App\Http\Controllers\Employee\LeaveSummaryController;
use App\Http\Controllers\Employee\AttendanceController;
use App\Http\Controllers\Employee\HolidayController;
use App\Http\Controllers\Employee\DashboardController;
use App\Http\Controllers\Employee\PolicyController;
use App\Http\Controllers\Employee\KraController;


Route::middleware(['auth:employee'])->prefix('employee')->name('employee.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/dashboard/mark-warning-shown', [DashboardController::class, 'markWarningShown'])->name('dashboard.mark-warning-shown');  // NEW: Route to mark warning as shown

    Route::get('/holidays', [HolidayController::class, 'index'])->name('holidays.index');

    // Policies
    Route::get('/policies', [PolicyController::class, 'index'])->name('policies.index');

    // KRA
    Route::get('/kra', [KraController::class, 'index'])->name('kra.index');
    
    // Punch In / Punch Out
    Route::get('/punches', [PunchController::class, 'index'])->name('punches.index');
    Route::post('/punches', [PunchController::class, 'store'])->name('punches.store');
    
    // Task routes
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
    Route::post('/tasks/save', [TaskController::class, 'save'])->name('tasks.save');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    
    // Attendance
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    
    // Leave applications
    Route::get('/leave-applications', [LeaveApplicationController::class, 'index'])->name('leave-applications.index');
    Route::post('/leave-applications', [LeaveApplicationController::class, 'store'])->name('leave-applications.store');
    Route::post('/leave-applications/{leave}/update', [LeaveApplicationController::class, 'update'])->name('leave-applications.update');
    Route::post('/leave-applications/{leave}/delete', [LeaveApplicationController::class, 'destroy'])->name('leave-applications.destroy');
    
    // Leave summary
    Route::get('/leave-summary', [LeaveSummaryController::class, 'index'])->name('leave-summary.index');
});