<?php

namespace Tests\Unit\Rmsapi;

use App\Jobs\Rmsapi\ProcessImporedProductsJob;
use App\Models\RmsapiProductImport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProcessImportedProductsJobTest extends TestCase
{
    public function test_if_processes_correctly()
    {
        // make sure there is nothing left
        RmsapiProductImport::query()->delete();

        factory(RmsapiProductImport::class)->create();

        $job = new ProcessImporedProductsJob();

        $job->handle();

        $unprocessedOrdersExists = RmsapiProductImport::query()
            ->whereNull('when_processed')
            ->exists();

        $this->assertFalse($unprocessedOrdersExists, 'Some products still not processed');

    }
}
