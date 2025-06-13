<?php 
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/manager/dashboard', function () {
    return Inertia::render('Manager/Dashboard');
})->middleware(['auth']);
