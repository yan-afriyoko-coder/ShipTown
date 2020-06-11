<?php

namespace Tests\External\Rmsapi;

use App\Jobs\Api2cart\ImportOrdersJob;
use App\Jobs\Rmsapi\ImportProductsJob;
use App\Jobs\Rmsapi\ProcessImportedProductsJob;
use App\Models\RmsapiConnection;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ImportProductsJobTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_if_job_runs()
    {
        Bus::fake();

        RmsapiConnection::query()->delete();

        factory(RmsapiConnection::class)->create();

        $job = new ImportProductsJob();

        $job->handle();

        Bus::assertDispatched(ProcessImportedProductsJob::class);
    }

}
