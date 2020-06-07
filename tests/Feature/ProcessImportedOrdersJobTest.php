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

        factory(Api2cartOrderImports::class)->create();

        $job = new ProcessImportedOrdersJob();

        $job->handle();

        $unprocessedOrdersExists = Api2cartOrderImports::query()
            ->whereNull('when_processed')
            ->exists();

        $this->assertFalse($unprocessedOrdersExists, 'Some orders still not processed');

    }
}
