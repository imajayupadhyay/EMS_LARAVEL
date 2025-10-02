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
use App\Http\Controllers\Admin\LeaveAssignmentController;
use App\Http\Controllers\Admin\LeaveApplicationController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\SalaryReportController;
use App\Http\Controllers\Admin\UserManageController;
use App\Http\Controllers\Admin\MarketerTrackingController;
use App\Http\Controllers\Admin\MarketerController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\KraController;


Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
Route::get('/attendance/{employeeId}/{date}', [AttendanceController::class, 'details']);


// Route::middleware(['auth'])->prefix('admin')->group(function () {
//     Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
// });

    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', fn () => Inertia::render('Admin/Dashboard'))->name('dashboard');





Route::middleware(['auth','admin'])->prefix('marketers')->name('marketers.')->group(function () {
    Route::get('/live-locations', [MarketerTrackingController::class, 'liveLocations'])
        ->name('live');
    Route::get('/create', fn () => Inertia::render('Admin/Marketers/Create'))
        ->name('create');
Route::get('/{id}/latest-location', [MarketerTrackingController::class, 'latestLocation'])
    ->name('latest-location');
        Route::get('/{id}/live', [MarketerTrackingController::class, 'fetchLiveLocation'])
    ->name('fetchLiveLocation');
    Route::get('/', [MarketerController::class, 'index'])->name('index');
    Route::post('/', [MarketerController::class, 'store'])->name('store');
    Route::get('/{id}/edit', fn ($id) => Inertia::render('Admin/Marketers/Edit', ['id' => $id]))
        ->name('edit');
    Route::get('/{id}', [MarketerController::class, 'show'])->name('show');
    Route::put('/{id}', [MarketerController::class, 'update'])->name('update');
    Route::delete('/{id}', [MarketerController::class, 'destroy'])->name('destroy');
});

Route::resource('policies', \App\Http\Controllers\Admin\PolicyController::class)->names('policies');
    Route::post('policies/{policy}/toggle', [\App\Http\Controllers\Admin\PolicyController::class,'toggle'])->name('policies.toggle');
    Route::post('policies/{policy}/delete', [\App\Http\Controllers\Admin\PolicyController::class, 'destroy'])->name('admin.policies.delete');

// Shift Management Routes
Route::get('/shifts', [App\Http\Controllers\Admin\ShiftController::class, 'index'])->name('shifts.index');
Route::post('/shifts', [App\Http\Controllers\Admin\ShiftController::class, 'store'])->name('shifts.store');
Route::post('/shifts/update', [App\Http\Controllers\Admin\ShiftController::class, 'update'])->name('shifts.update');
Route::post('/shifts/delete', [App\Http\Controllers\Admin\ShiftController::class, 'destroy'])->name('shifts.destroy');

// KRA Management Routes - Following your existing pattern
Route::get('/kras', [KraController::class, 'index'])->name('kras.index');
Route::post('/kras', [KraController::class, 'store'])->name('kras.store');
Route::post('/kras/{kra}', [KraController::class, 'update'])->name('kras.update');
Route::post('/kras/{kra}/delete', [KraController::class, 'destroy'])->name('kras.destroy');
Route::post('/kras/{kra}/toggle-status', [KraController::class, 'toggleStatus'])->name('kras.toggle-status');



Route::get('/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
// Show single employee (AJAX JSON) — VIEW modal
Route::get('/employees/manage/{employee}', [EmployeeManageController::class, 'show'])
    ->name('employees.manage.show');


    // List employees
    Route::get('/employees/manage', [EmployeeManageController::class, 'index'])
        ->name('employees.manage');

    // Update: accept both a POST with _method=PUT or a real PUT
    Route::match(['post','put'], '/employees/manage/{employee}', [EmployeeManageController::class, 'update'])
        ->name('employees.manage.update');

    // Delete employee
    Route::post('/employees/manage/{employee}/delete', [EmployeeManageController::class, 'destroy'])
        ->name('employees.manage.destroy');


Route::get('/employees', function() {
    return redirect()->route('admin.employees.manage');
});

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

Route::get('/leave-assignments', [LeaveAssignmentController::class, 'index'])->name('leave-assignments.index');
    Route::post('/leave-assignments', [LeaveAssignmentController::class, 'store'])->name('leave-assignments.store');
    Route::post('/leave-assignments/{leaveAssignment}/update', [LeaveAssignmentController::class, 'update'])->name('leave-assignments.update');
    Route::post('/leave-assignments/{leaveAssignment}/delete', [LeaveAssignmentController::class, 'destroy'])->name('leave-assignments.destroy');
Route::get('/leave-applications', [LeaveApplicationController::class, 'index'])->name('leave-applications.index');
Route::post('/leave-applications/{leaveApplication}/update-status', [LeaveApplicationController::class, 'updateStatus'])->name('leave-applications.updateStatus');
Route::post('/leave-applications/{leaveApplication}/update', [LeaveApplicationController::class, 'update'])->name('leave-applications.update');


Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
Route::post('/notifications/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markSingleAsRead'])->name('notifications.markSingleAsRead');
Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
Route::post('/notifications/clear', [NotificationController::class, 'clear'])->name('notifications.clear');

Route::get('/salary-report', [SalaryReportController::class, 'index'])->name('salary-report.index');
Route::post('/salary-report/update', [SalaryReportController::class, 'update'])->name('salary-report.update');
Route::post('/salary-report/finalize', [SalaryReportController::class, 'finalize'])
    ->name('salary-report.finalize');
Route::get('/salary-report/export', [SalaryReportController::class, 'export'])->name('salary-report.export');
Route::get('/salary-report/payslip/{salary}', [SalaryReportController::class, 'payslip'])
    ->name('salary-report.payslip')
    ->whereNumber('salary');

// Index + store stay same
Route::get('/users', [UserManageController::class, 'index'])->name('users.index');
Route::post('/users', [UserManageController::class, 'store'])->name('users.store');

// Accept POST with method spoofing for update + delete
Route::post('/users/{user}', [UserManageController::class, 'update'])->name('users.update');
Route::post('/users/{user}/delete', [UserManageController::class, 'destroy'])->name('users.destroy');




});
