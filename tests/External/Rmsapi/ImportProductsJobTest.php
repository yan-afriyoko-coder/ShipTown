<?php

namespace Tests\External\Rmsapi;

use App\Modules\Rmsapi\src\Models\RmsapiConnection;
use App\Modules\Rmsapi\src\Models\RmsapiProductImport;
use App\Modules\Rmsapi\src\Jobs\FetchUpdatedProductsJob;
use App\Modules\Rmsapi\src\Jobs\ProcessProductImports;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ImportProductsJobTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @throws \Exception
     *
     * @return void
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
        Bus::assertDispatched(ProcessProductImports::class);
    }
}
