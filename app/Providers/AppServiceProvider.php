<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\StockMovement;
use App\Observers\StockMovementObserver;



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
        StockMovement::observe(StockMovementObserver::class);

        // Register JWT middleware alias if router available
        if ($this->app->bound('router')) {
            $this->app['router']->aliasMiddleware('auth.jwt', \App\Http\Middleware\ValidateJwt::class);
            $this->app['router']->aliasMiddleware('role', \App\Http\Middleware\RoleMiddleware::class);
        }

    }
    
}

