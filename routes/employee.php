<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Employee\PunchController;
use App\Http\Controllers\Employee\TaskController;

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




    // Future: Leave Management
    // Route::get('/leaves', [LeaveController::class, 'index'])->name('leaves.index');
    // Route::post('/leaves', [LeaveController::class, 'store'])->name('leaves.store');

    // Future: Assigned Tasks
    // Route::get('/assignments', [AssignmentController::class, 'index'])->name('assignments.index');
});
