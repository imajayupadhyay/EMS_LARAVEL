<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Manager\DashboardController;
use App\Http\Controllers\Manager\EmployeeManageController;

Route::middleware(['auth'])
    ->prefix('manager')
    ->as('manager.')
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::resource('employees', App\Http\Controllers\Manager\EmployeeManageController::class);


        // future manager routes can go here
        // Route::resource('employees', EmployeeManageController::class);
    });
