<?php

namespace Tests\Feature;

use App\Jobs\Api2cart\ProcessImportedOrdersJob;
use App\Models\Api2cartOrderImports;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProcessImportedOrdersJobTest extends TestCase
{
    public function test_if_processes_correctly() {

        $this->doesNotPerformAssertions();

        factory(Api2cartOrderImports::class)->create();

        $job = new ProcessImportedOrdersJob();

        $job->handle();

    }
}
