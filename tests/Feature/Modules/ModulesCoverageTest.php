<?php

namespace Tests\Feature\Modules;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Tests\TestCase;

class ModulesCoverageTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test to make sure all routes have minimum one test file.
     *
     * @return void
     */
    public function test_if_all_modules_have_test_file()
    {
        $modulesList = File::directories('app/modules');

        $expectedTestsList = collect($modulesList)->map(function ($moduleDirectory) {
            $testPath = Str::replaceArray('app/modules/', ['/tests/Feature/ModulesNew/'], $moduleDirectory);

            return app()->basePath(). $testPath . '/BasicModuleTest.php';
        });

        $expectedTestsList->each(function ($testName) {
            $this->assertFileExists(
                $testName,
                $testName . ' test is missing. Run "php artisan app:generate-modules-tests"'
            );
        });
    }
}
