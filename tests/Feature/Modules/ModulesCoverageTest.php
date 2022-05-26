<?php

namespace Tests\Feature\Modules;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Tests\TestCase;

class ModulesCoverageTest extends TestCase
{
    /**
     * A basic test to make sure all routes have minimum one test file.
     *
     * @return void
     */
    public function test_if_all_modules_have_test_file()
    {
        $modulesList = File::directories('app/Modules');

        $expectedTestsList = collect($modulesList)->map(function ($moduleDirectory) {
            $testPath = Str::replaceArray('app/Modules/', ['/tests/Feature/Modules/'], $moduleDirectory);

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
