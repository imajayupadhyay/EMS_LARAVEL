<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Manager\DashboardController;
use App\Http\Controllers\Manager\EmployeeManageController;
use App\Http\Controllers\Manager\ShiftController; 
use App\Http\Controllers\Manager\EmployeeShiftController;
use App\Http\Controllers\Manager\AttendanceController;

Route::middleware(['auth'])
    ->prefix('manager')
    ->as('manager.')
    ->group(function () {
        
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Shift Management Routes - MOVED BEFORE employees group to avoid conflicts
        Route::get('/shifts', [ShiftController::class, 'index'])->name('shifts.index');
        
        // Employee Shift Assignment Routes - Changed URL to avoid conflict with /{employee} route
        Route::get('/employee-shifts', [EmployeeShiftController::class, 'index'])->name('employees.shifts.index');
        Route::post('/employees/{employee}/assign-shift', [EmployeeShiftController::class, 'assignShift'])->name('employees.assign-shift');
        
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
        
         Route::prefix('attendance')->name('attendance.')->group(function () {
            Route::get('/', [AttendanceController::class, 'index'])->name('index');
            Route::get('/export', [AttendanceController::class, 'export'])->name('export');
            Route::get('/details/{employeeId}/{date}', [AttendanceController::class, 'details'])->name('details');
        });
        // Future manager routes can go here
        // Route::resource('reports', ReportController::class);
        // Route::resource('departments', DepartmentController::class);
        // Route::resource('designations', DesignationController::class);
        
    });