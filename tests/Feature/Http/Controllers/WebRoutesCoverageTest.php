<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use PhpParser\Node\Stmt\Return_;
use Tests\TestCase;

class WebRoutesCoverageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test to make sure all routes have minimum one test file.
     *
     * @return void
     */
    public function test_if_all_web_routes_have_test_file()
    {
        Artisan::call('route:list --json --env=production');

        $routes = collect(json_decode(Artisan::output()))
            ->filter(function ($route) {
                $isNotApiRoute = ! Str::startsWith($route->uri, 'api');
                $isGetMethod = $route->method === 'GET|HEAD';
                $isNotDevRoute = ! Str::startsWith($route->uri, '_');

                return $isNotApiRoute && $isGetMethod && $isNotDevRoute;
            });

        $this->assertNotEmpty($routes, 'Artisan route:list command did not return any routes');

        $routes->each(function ($route) {
            $testName = $this->getTestName($route);

            $fileName = app()->basePath().'/tests/Feature/Routes/Web/'.$testName.'.php';

            $this->assertFileExists(
                $fileName,
                'Test missing. Please run "php artisan app:generate-routes-tests"'
            );
        });
    }

    /**
     * @param $route
     *
     * @return string
     */
    public function getTestName($route): string
    {
        $routeName = $route->uri . 'Test';

        $routeName = str_replace('-', '_', $routeName);
        $routeName = str_replace('.', '_', $routeName);
        $routeName = str_replace('{', '', $routeName);
        $routeName = str_replace('}', '', $routeName);

        return Str::ucfirst($routeName);
    }
}
