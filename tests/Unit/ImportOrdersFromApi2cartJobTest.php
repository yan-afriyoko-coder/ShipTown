<?php

namespace Tests\Unit;

use App\Jobs\ImportOrdersFromApi2cartJob;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ImportOrdersFromApi2cartJobTest extends TestCase
{
    public function test_if_job_runs_without_exceptions()
    {
        $job = new ImportOrdersFromApi2cartJob();
        $job->handle();
        $this->assertTrue(true);
    }
}
