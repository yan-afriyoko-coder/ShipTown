<?php

namespace Tests\Feature\jobs;

use App\Jobs\JobImportOrderApi2Cart;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class import_orders_api2cart_test extends TestCase
{
    public function test_successful_job_execution()
    {
        $job = new JobImportOrderApi2Cart();

        $job->handle();
    }
}
