<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\DesignationController;
use App\Http\Controllers\Admin\EmployeeManageController;
use App\Http\Controllers\Admin\LocationController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
| All routes here are prefixed with /admin and use the 'auth' middleware.
| These routes use Inertia for rendering Vue pages inside the admin layout.
*/

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    
    // Admin Dashboard
    Route::get('/dashboard', fn() => Inertia::render('Admin/Dashboard'))->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Employee Management
    |--------------------------------------------------------------------------
    */
    Route::get('/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
    Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');

    Route::get('/employees/manage', [EmployeeManageController::class, 'index'])->name('employees.manage');
    Route::post('/employees/manage/{employee}', [EmployeeManageController::class, 'update'])->name('employees.manage.update');
    Route::post('/employees/manage/{employee}/delete', [EmployeeManageController::class, 'destroy'])->name('employees.manage.destroy');
    Route::get('/employees', [EmployeeManageController::class, 'index'])->name('employees.index');

    /*
    |--------------------------------------------------------------------------
    | Organization Settings
    |--------------------------------------------------------------------------
    */
    Route::resource('departments', DepartmentController::class)->except(['show', 'create', 'edit']);
    Route::resource('designations', DesignationController::class)->except(['show', 'create', 'edit']);
    Route::resource('locations', LocationController::class)->except(['show', 'create', 'edit']);

    /*
    |--------------------------------------------------------------------------
    | Role Management (optional/future)
    |--------------------------------------------------------------------------
    */
    Route::get('/roles', fn() => Inertia::render('Admin/ManageRoles/Index'))->name('roles.index');
});
