<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Marketer\AuthController;
use App\Http\Controllers\Marketer\PunchController;

Route::prefix('marketer')->name('marketer.')->group(function () {
    // Login
    Route::get('/login', fn() => Inertia::render('Marketer/Login'))->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Protected Routes
    Route::middleware('auth:marketer')->group(function () {
        Route::get('/dashboard', fn() => Inertia::render('Marketer/Dashboard'))->name('dashboard');

        Route::post('/punch-in', [PunchController::class, 'punchIn'])->name('punch.in');
        Route::post('/punch-out', [PunchController::class, 'punchOut'])->name('punch.out');
        Route::post('/update-location', [PunchController::class, 'updateLocation'])->name('update.location');

Route::get('/status', [PunchController::class, 'status'])->name('status');



Route::get('/today-worked', [PunchController::class, 'todayWorked'])->name('marketer.todayWorked');


    });
});
