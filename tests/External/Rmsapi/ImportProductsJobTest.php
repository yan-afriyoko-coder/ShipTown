<?php

namespace Tests\External\Rmsapi;

use App\Jobs\Rmsapi\ImportProductsJob;
use App\Models\RmsapiConnection;
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
        Event::fake();

        RmsapiConnection::query()->delete();

        $connection = factory(RmsapiConnection::class)->create();

        $job = new ImportProductsJob($connection);

        $job->handle();

        // we just check for no exceptions
        $this->assertTrue(true);
    }

}
