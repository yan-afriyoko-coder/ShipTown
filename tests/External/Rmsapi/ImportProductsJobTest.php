<?php

namespace Tests\External\Rmsapi;

use App\Models\RmsapiConnection;
use App\Models\RmsapiProductImport;
use App\Modules\Rmsapi\src\Jobs\FetchUpdatedProductsJob;
use App\Modules\Rmsapi\src\Jobs\ProcessImportedBatch;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ImportProductsJobTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     * @throws \Exception
     */
    public function testIfJobRuns()
    {
        Bus::fake();
        Event::fake();

        // we want clean data
        RmsapiConnection::query()->delete();
        RmsapiProductImport::query()->delete();

        $connection = factory(RmsapiConnection::class)->create();

        $job = new FetchUpdatedProductsJob($connection->id);

        $job->handle();

        $this->assertTrue(RmsapiProductImport::query()->exists(), 'No imports have been made');
        Bus::assertDispatched(ProcessImportedBatch::class);
    }
}
