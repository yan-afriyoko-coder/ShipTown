<?php

namespace Tests\Feature;

use App\Jobs\Api2cart\ProcessImportedOrdersJob;
use App\Models\Api2cartOrderImports;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Class ProcessImportedOrdersJobTest
 * @package Tests\Feature
 */
class ProcessImportedOrdersJobTest extends TestCase
{
    public function test_if_processes_correctly() {

        // this is very basic test
        // we only want to make sure that no Exceptions is thrown
        $this->doesNotPerformAssertions();

        factory(Api2cartOrderImports::class)->create();

        $job = new ProcessImportedOrdersJob();

        $job->handle();

    }
}
