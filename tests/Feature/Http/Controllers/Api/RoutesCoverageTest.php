<?php

namespace Tests\Feature\Http\Controllers\Api;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;
use Tests\TestCase;

class RoutesCoverageTest extends TestCase
{
    /**
     * A basic test to make sure all routes have minimum one test file
     *
     * @return void
     */
    public function test_if_all_routes_have_test_file()
    {
        Artisan::call('route:list --json --path=api');

        $routes = collect(json_decode(Artisan::output()));

        $routes->each(function ($route) {
            $testName = $this->getTestName($route);

            $fileName = app()->basePath() . '/tests/Feature/'. $testName . '.php';

            $this->assertFileExists(
                $fileName,
                'Route test missing. Please run "php artisan app:generate-api-routes-tests"'
            );
        });
    }

    /**
     * @param $route
     * @return string
     */
    public function getTestName($route): string
    {
        // $sample_action = 'App\\Http\\Controllers\\Api\\Settings\\UserMeController@index'
        $controllerName = Str::before($route->action, '@');
        $methodName = Str::after($route->action, '@');

        $testDirectory = Str::after($controllerName, 'App\\') ;
        $testName = $testDirectory . '\\' . Str::ucfirst($methodName) . 'Test';

        // $sample_output = 'Http/Controllers/Api/Settings/UserMeController/IndexTest'
        return str_replace('\\', '/', $testName);
    }
}
