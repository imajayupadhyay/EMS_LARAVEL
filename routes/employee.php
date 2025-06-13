<?php
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['auth'])->group(function () {
    Route::get('/employee/dashboard', function () {
        return Inertia::render('Employee/Dashboard');
    });
});
