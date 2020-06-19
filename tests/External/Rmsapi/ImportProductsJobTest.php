<?php

namespace Tests\External\Rmsapi;

use App\Jobs\Rmsapi\ImportProductsJob;
use App\Jobs\Rmsapi\ProcessImportedProductsJob;
use App\Models\RmsapiConnection;
use App\Models\RmsapiImportedProduct;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ImportProductsJobTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     * @throws \Exception
     */
    public function test_if_job_runs()
    {
        Bus::fake();
        Event::fake();

        // we want clean data
        RmsapiConnection::query()->delete();
        RmsapiImportedProduct::query()->delete();

        $connection = factory(RmsapiConnection::class)->create();

        $job = new ImportProductsJob($connection);

        $job->handle();

        $this->assertTrue(RmsapiImportedProduct::query()->exists(), 'No imports have been made');
        Bus::assertDispatched(ProcessImportedProductsJob::class);

    }

}
