<?php

namespace Tests\Feature\Modules\Rmsapi;

use App\Jobs\SyncRequestJob;
use App\Modules\Rmsapi\src\Jobs\DispatchImportJobs;
use App\Modules\Rmsapi\src\Jobs\ImportAllJob;
use App\Modules\Rmsapi\src\Jobs\ImportShippingsJob;
use App\Modules\Rmsapi\src\Models\RmsapiConnection;
use App\Modules\Rmsapi\src\RmsapiModuleServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class JobsDispatchTests extends TestCase
{
    use RefreshDatabase;

    public function test_if_dispatches_import_jobs()
    {
        Bus::fake();

        RmsapiModuleServiceProvider::enableModule();

        RmsapiConnection::factory()->create();

        DispatchImportJobs::dispatchNow();

        Bus::assertDispatched(ImportAllJob::class);
        Bus::assertDispatched(ImportShippingsJob::class);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIfDispatchesJobs()
    {
        Bus::fake();

        RmsapiModuleServiceProvider::enableModule();

        RmsapiConnection::factory()->create();

        (new SyncRequestJob())->handle();

        Bus::assertDispatched(DispatchImportJobs::class);
    }
}
