<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Support\Facades\Mail;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

/*
|--------------------------------------------------------------------------
| Utility Routes (for testing emails, config, etc.)
|--------------------------------------------------------------------------
*/
Route::get('/test-mail', function () {
    Mail::raw('This is a test email from EMS system.', function ($message) {
        $message->to('ajayupadhyay030@gmail.com')
                ->subject('Test Mail - EMS');
    });
    return 'Test mail sent (if config is correct)!';
});

Route::get('/check-mail-config', function () {
    return [
        'mailer' => config('mail.mailer'),
        'host' => config('mail.host'),
        'port' => config('mail.port'),
        'username' => config('mail.username'),
        'encryption' => config('mail.encryption'),
        'from' => config('mail.from'),
    ];
});

/*
|--------------------------------------------------------------------------
| Admin/Manager Dashboard (using default web guard)
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Employee Dashboard (using employee guard)
|--------------------------------------------------------------------------
*/
Route::get('/employee/dashboard', function () {
    return Inertia::render('Employee/Dashboard');
})->middleware('auth:employee')->name('employee.dashboard');

/*
|--------------------------------------------------------------------------
| Authenticated Profile Routes (for web users only - admin/manager)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Load Modular Route Files
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
require __DIR__.'/employee.php';
require __DIR__.'/admin.php';
require __DIR__.'/manager.php';
