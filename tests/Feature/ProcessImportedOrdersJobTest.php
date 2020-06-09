<?php

namespace Tests\Feature;

use App\Jobs\Api2cart\ProcessImportedOrdersJob;
use App\Models\Api2CartOrderImports;
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

        Api2CartOrderImports::query()->delete();

        factory(Api2CartOrderImports::class)->create();

        $job = new ProcessImportedOrdersJob();

        $job->handle();

        $unprocessedOrdersExists = Api2CartOrderImports::query()
            ->whereNull('when_processed')
            ->exists();

        $this->assertFalse($unprocessedOrdersExists, 'Some orders still not processed');

    }
}
