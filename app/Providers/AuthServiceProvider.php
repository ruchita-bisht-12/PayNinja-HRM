<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Define the 'manage_payroll_settings' gate
        Gate::define('manage_payroll_settings', function ($user) {
            // Allow superadmin, admin, and company_admin roles
            return $user->hasRole(['superadmin', 'admin', 'company_admin']);
        });

        // You can define other gates or policies here
    }
}
