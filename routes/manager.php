<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Manager\DashboardController;

Route::middleware(['auth'])
    ->prefix('manager')
    ->as('manager.')
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // future manager routes can go here
        // Route::resource('employees', EmployeeManageController::class);
    });
