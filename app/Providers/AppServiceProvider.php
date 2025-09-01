<?php

namespace App\Providers;
use Inertia\Inertia;
use Illuminate\Support\Facades\Session;

use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
public function boot()
{
    Inertia::share([
        'flash' => function () {
            return [
                'success' => Session::get('success'),
                'error' => Session::get('error'),
            ];
        },
        // ✅ Share logged in user (works for both web + employee guards)
        'auth' => function () {
            if (auth()->check()) {
                return ['user' => auth()->user()];
            }

            if (auth('employee')->check()) {
                return ['user' => auth('employee')->user()];
            }

            return ['user' => null];
        },
    ]);
}
    
}
