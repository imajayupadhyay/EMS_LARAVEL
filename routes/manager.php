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
        
        // Employee Management Routes
        Route::prefix('employees')->name('employees.')->group(function () {
            // Standard CRUD routes
            Route::get('/', [EmployeeManageController::class, 'index'])->name('index');
            Route::get('/create', [EmployeeManageController::class, 'create'])->name('create');
            Route::post('/', [EmployeeManageController::class, 'store'])->name('store');
            Route::get('/{employee}', [EmployeeManageController::class, 'show'])->name('show');
            Route::get('/{employee}/edit', [EmployeeManageController::class, 'edit'])->name('edit');
            
            // Accept both PUT and PATCH for update
            Route::match(['PUT', 'PATCH'], '/{employee}', [EmployeeManageController::class, 'update'])->name('update');
            
            Route::delete('/{employee}', [EmployeeManageController::class, 'destroy'])->name('destroy');
            
            // Additional enhanced functionality routes
            Route::post('/bulk-delete', [EmployeeManageController::class, 'bulkDelete'])->name('bulk-delete');
            Route::get('/export/csv', [EmployeeManageController::class, 'export'])->name('export');
        });
        
        // Future manager routes can go here
        // Route::resource('reports', ReportController::class);
        // Route::resource('departments', DepartmentController::class);
        // Route::resource('designations', DesignationController::class);
        
    });