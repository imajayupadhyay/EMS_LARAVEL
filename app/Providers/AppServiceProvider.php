<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Define Admin Gate
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
