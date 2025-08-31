<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Define your route model bindings, pattern filters, etc.
     */
    public function boot(): void
    {
        $this->routes(function () {

            // Default web routes
            Route::middleware('web')
                ->group(base_path('routes/web.php'));

            // Auth routes (optional if not already in web.php)
            Route::middleware('web')
                ->group(base_path('routes/auth.php'));

            // Admin routes
            Route::middleware('web')
                ->group(base_path('routes/admin.php'));

            // Manager routes
            Route::middleware('web')
                ->group(base_path('routes/manager.php'));

            // ✅ Employee routes
            Route::middleware('web')
                ->group(base_path('routes/employee.php'));

            Route::middleware('web')
                ->group(base_path('routes/marketer.php'));

        });
    }
}
