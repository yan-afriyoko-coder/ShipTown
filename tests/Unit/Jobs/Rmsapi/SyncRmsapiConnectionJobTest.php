<?php

namespace Tests\Unit\Jobs\Rmsapi;

use App\Jobs\Rmsapi\ImportProductsJob;
use App\Jobs\Rmsapi\ProcessImportedProductsJob;
use App\Jobs\Rmsapi\SyncRmsapiConnectionJob;
use App\Models\RmsapiConnection;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SyncRmsapiConnectionJobTest extends TestCase
{
    public function test_if_dispatches_required_jobs()
    {
        Bus::fake();

        RmsapiConnection::query()->delete();

        $connection = factory(RmsapiConnection::class)->create();

        $job = new SyncRmsapiConnectionJob($connection);

        $job->handle();

        Bus::assertDispatched(ImportProductsJob::class);
//        Bus::assertDispatched(ProcessImportedProductsJob::class);
    }
}
