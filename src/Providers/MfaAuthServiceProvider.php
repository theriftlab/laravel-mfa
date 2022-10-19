<?php

namespace Mfa\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Mfa\Http\Middleware\Mfa;
use Mfa\Services\MfaAuth;

class MfaAuthServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->make(Router::class)->aliasMiddleware('mfa', Mfa::class);
        $this->app->singleton(MfaAuth::class, fn () => new MfaAuth(Auth::user()));

        $this->mergeConfigFrom(__DIR__.'/../../config/config.php', 'mfa');
        $this->loadMigrationsFrom(__DIR__.'/../../database/migrations');
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.php');
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'mfa');

        $this->publishes([
            __DIR__.'/../../config/config.php' => config_path('mfa.php')
        ], 'mfa-config');

        $this->publishes([
            __DIR__.'/../../database/migrations/' => database_path('migrations')
        ], 'mfa-migrations');

        $this->publishes([
            __DIR__.'/../../resources/views/emails/' => resource_path('views/emails')
        ], 'mfa-views');
    }
}
