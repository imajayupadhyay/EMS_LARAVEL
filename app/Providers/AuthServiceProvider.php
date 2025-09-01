<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [];

    public function boot(): void
    {
        $this->registerPolicies();

        Gate::define('isAdmin', function ($user) {
            return $user->role === 'admin';
        });

         // (Optional) Define Manager Gate if needed
        Gate::define('isManager', function ($user) {
            return $user->role === 'manager';
        });

        // (Optional) Define Employee Gate if needed
        Gate::define('isEmployee', function ($user) {
            return $user->role === 'employee';
        });
    }
}
