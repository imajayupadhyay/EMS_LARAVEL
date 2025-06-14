<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\EmployeeManageController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\DesignationController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\TaskController;
use App\Http\Controllers\Admin\LeaveTypeController;
use App\Http\Controllers\Admin\HolidayController;

Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', fn () => Inertia::render('Admin/Dashboard'))->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Employee Management
    |--------------------------------------------------------------------------
    */
    Route::get('/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
    Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');

    Route::get('/employees', [EmployeeManageController::class, 'index'])->name('employees.index');
    Route::get('/employees/manage', [EmployeeManageController::class, 'index'])->name('employees.manage');
    Route::post('/employees/manage/{employee}', [EmployeeManageController::class, 'update'])->name('employees.manage.update');
    Route::post('/employees/manage/{employee}/delete', [EmployeeManageController::class, 'destroy'])->name('employees.manage.destroy');

    /*
    |--------------------------------------------------------------------------
    | Department & Designation
    |--------------------------------------------------------------------------
    */
    Route::resource('departments', DepartmentController::class)->except(['show', 'create', 'edit']);
    Route::resource('designations', DesignationController::class)->except(['show', 'create', 'edit']);

    /*
    |--------------------------------------------------------------------------
    | Locations - Using POST for update/delete with _method spoofing
    |--------------------------------------------------------------------------
    */
Route::get('/locations', [LocationController::class, 'index'])->name('locations.index');
Route::post('/locations', [LocationController::class, 'store'])->name('locations.store');
Route::post('/locations/update', [LocationController::class, 'update'])->name('locations.update');
Route::post('/locations/delete', [LocationController::class, 'destroy'])->name('locations.destroy');



    /*
    |--------------------------------------------------------------------------
    | Role Management (Optional)
    |--------------------------------------------------------------------------
    */
    Route::get('/roles', fn () => Inertia::render('Admin/ManageRoles/Index'))->name('roles.index');

    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::get('/attendance/export', [AttendanceController::class, 'export'])->name('attendance.export');
  
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');



    Route::get('/leave-types', [LeaveTypeController::class, 'index'])->name('leave-types.index');
    Route::post('/leave-types', [LeaveTypeController::class, 'store'])->name('leave-types.store');
    Route::post('/leave-types/{leaveType}', [LeaveTypeController::class, 'update'])->name('leave-types.update');
    Route::post('/leave-types/{leaveType}/delete', [LeaveTypeController::class, 'destroy'])->name('leave-types.destroy');


    Route::get('/holidays', [HolidayController::class, 'index'])->name('holidays.index');
Route::post('/holidays', [HolidayController::class, 'store'])->name('holidays.store');
Route::post('/holidays/{holiday}', [HolidayController::class, 'update'])->name('holidays.update');
Route::post('/holidays/{holiday}/delete', [HolidayController::class, 'destroy'])->name('holidays.destroy');

});
