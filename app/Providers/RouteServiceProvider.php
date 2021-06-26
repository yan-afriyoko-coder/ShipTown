<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

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
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapPublicRoutes();

        $this->mapUserWebRoutes();

        $this->mapUserApiRoutes();

        $this->mapAdminWebRoutes();

        $this->mapAdminApiRoutes();
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapPublicRoutes()
    {
        Route::middleware(['web'])
             ->namespace($this->namespace)
             ->group(base_path('routes/public.php'));
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapUserWebRoutes()
    {
        Route::middleware(['web', 'auth'])
             ->namespace($this->namespace)
             ->group(base_path('routes/user/web.php'));
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapAdminWebRoutes()
    {
        Route::middleware(['web','auth', 'role:admin'])
            ->prefix('admin')
            ->namespace($this->namespace)
            ->group(base_path('routes/admin/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapUserApiRoutes()
    {
        Route::middleware(['api','auth:api'])
            ->prefix('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/user/api.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapAdminApiRoutes()
    {
        Route::middleware(['api','auth:api', 'role:admin'])
            ->prefix('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/admin/api.php'));
    }
}
