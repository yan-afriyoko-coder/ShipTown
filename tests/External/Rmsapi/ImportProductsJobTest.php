<?php

namespace Tests\External\Rmsapi;

use App\Modules\Rmsapi\src\Jobs\ImportProductsJob;
use App\Jobs\Rmsapi\ProcessImportedProductsJob;
use App\Models\RmsapiConnection;
use App\Models\RmsapiProductImport;
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
        RmsapiProductImport::query()->delete();

        $connection = factory(RmsapiConnection::class)->create();

        $job = new ImportProductsJob($connection->id);

        $job->handle();

        $this->assertTrue(RmsapiProductImport::query()->exists(), 'No imports have been made');
        Bus::assertDispatched(ProcessImportedProductsJob::class);

    }

}
