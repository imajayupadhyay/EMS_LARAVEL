<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Employee\PunchController;
use App\Http\Controllers\Employee\TaskController;
use App\Http\Controllers\Employee\LeaveApplicationController;
use App\Http\Controllers\Employee\LeaveSummaryController;


Route::middleware(['auth'])->prefix('employee')->name('employee.')->group(function () {

    // Dashboard
    Route::get('/dashboard', fn() => Inertia::render('Employee/Dashboard'))->name('dashboard');

    // Punch In / Punch Out
    Route::get('/punches', [PunchController::class, 'index'])->name('punches.index');
    Route::post('/punches', [PunchController::class, 'store'])->name('punches.store');
    

    // Task routes (clean single save + delete)
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');
  Route::post('/tasks/save', [TaskController::class, 'save'])->name('tasks.save');
Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');

Route::get('/attendance', [\App\Http\Controllers\Employee\AttendanceController::class, 'index'])->name('attendance.index');



Route::get('/leave-applications', [LeaveApplicationController::class, 'index'])->name('leave-applications.index');
    Route::post('/leave-applications', [LeaveApplicationController::class, 'store'])->name('leave-applications.store');
    Route::post('/leave-applications/{leave}/update', [LeaveApplicationController::class, 'update'])->name('leave-applications.update');
    Route::post('/leave-applications/{leave}/delete', [LeaveApplicationController::class, 'destroy'])->name('leave-applications.destroy');


    Route::get('/leave-summary', [\App\Http\Controllers\Employee\LeaveSummaryController::class, 'index'])->name('leave-summary.index');

});
