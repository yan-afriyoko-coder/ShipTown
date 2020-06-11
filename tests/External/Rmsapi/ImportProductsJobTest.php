<?php

namespace Tests\External\Rmsapi;

use App\Jobs\Rmsapi\ImportProductsJob;
use App\Models\RmsapiConnection;
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
        RmsapiConnection::query()->delete();

        factory(RmsapiConnection::class)->create();

        $job = new ImportProductsJob();

        $job->handle();

        // we just checking if there is no exceptions
        // this test only has to pass
        $this->assertTrue(true);
    }

}
