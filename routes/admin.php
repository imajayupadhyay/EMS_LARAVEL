<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\DesignationController;
use App\Http\Controllers\Admin\EmployeeManageController;
/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
| All routes here are prefixed with /admin and use the 'auth' middleware.
| These routes use Inertia for rendering Vue pages inside the admin layout.
*/

Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {

    // Admin Dashboard
    Route::get('/dashboard', function () {
        return Inertia::render('Admin/Dashboard');
    })->name('dashboard');

    // Employee Management
    Route::get('/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
    Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
    // Optionally later:
    // Route::get('/employees', [EmployeeController::class, 'index'])->name('employees.index');
    // Route::get('/employees/{id}/edit', [EmployeeController::class, 'edit'])->name('employees.edit');
});

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/roles', fn() => Inertia::render('Admin/ManageRoles/Index'))->name('roles.index');

    Route::resource('departments', DepartmentController::class)->except(['show', 'create', 'edit']);
    Route::resource('designations', DesignationController::class)->except(['show', 'create', 'edit']);

});


Route::prefix('admin')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/employees/manage', [EmployeeManageController::class, 'index'])->name('employees.manage');
    Route::post('/employees/manage/{employee}', [EmployeeManageController::class, 'update'])->name('employees.manage.update');
    Route::post('/employees/manage/{employee}/delete', [EmployeeManageController::class, 'destroy'])->name('employees.manage.destroy');
    Route::get('/employees/manage', [EmployeeManageController::class, 'index'])->name('employees.index');
});


