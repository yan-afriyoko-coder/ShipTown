<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * @var string
     */
    public const HOME = '/dashboard';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            $this->mapPublicRoutes();

            $this->mapWebRoutes();

            $this->mapApiRoutes();
        });
    }

    /**
     * Define the "public" web routes for the application.
     *
     * These routes are PUBLICLY accessible !!!!
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapPublicRoutes()
    {
        Route::middleware(['web'])
            ->group(base_path('routes/public.php'));
    }

    /**
     * Define the "web" user routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware(['web', 'auth', 'twofactor'])
            ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" user routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::middleware(['auth:api', 'bindings'])
            ->prefix('api')
            ->group(base_path('routes/api.php'));
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
