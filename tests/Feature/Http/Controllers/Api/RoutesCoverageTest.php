<?php

namespace Tests\Feature\Http\Controllers\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Tests\TestCase;

class RoutesCoverageTest extends TestCase
{
    /**
     * A basic test to make sure all routes have minimum one test file.
     *
     * @return void
     */
    public function test_if_all_api_routes_have_test_file()
    {
        Artisan::call('route:list --json --path=api/ --env=production');

        $artisanOutput = json_decode(Artisan::output());

        collect($artisanOutput)
            ->map(function ($route) {
                $fullFileName = app()->basePath();
                $fullFileName .= '/tests/Feature/';
                $fullFileName .= $this->getTestFileName($route);
                $fullFileName .= '.php';

                return $fullFileName;
            })
            ->each(function ($fileName) {
                $this->assertFileExists($fileName, 'Run "php artisan app:generate-routes-tests"');
            });

        $this->assertNotEmpty($artisanOutput, 'Artisan route:list command did not return any routes');
    }

    /**
     * @param $route
     *
     * @return string
     */
    public function getTestFileName($route): string
    {
        // $sample_action = 'App\\Http\\Controllers\\Api\\Settings\\UserMeController@index'
        $controllerName = Str::before($route->action, '@');
        $methodName = Str::after($route->action, '@');

        $testDirectory = Str::after($controllerName, 'App\\');
        $testName = $testDirectory.'\\'.Str::ucfirst($methodName).'Test';

        // $sample_output = 'Http/Controllers/Api/Settings/UserMeController/IndexTest'
        return str_replace('\\', '/', $testName);
    }
}
