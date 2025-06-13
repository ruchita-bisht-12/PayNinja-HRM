<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Config;
use Illuminate\Pagination\Paginator;
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
    public function boot(): void
    {

        Paginator::useBootstrap();
        // Force HTTPS in production or when behind a proxy
        if ($this->app->environment('production') || 
            (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')) {
            URL::forceScheme('https');
            $this->app['request']->server->set('HTTPS', 'on');
        }
        
        // Set secure cookie flags
        if (config('session.secure')) {
            Config::set('session.secure', true);
            Config::set('session.same_site', 'none');
        }
    }
}
