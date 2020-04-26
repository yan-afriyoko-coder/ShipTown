<?php

namespace Tests\External;

use App\Jobs\ImportOrdersFromApi2cartJob;
use App\Managers\CompanyConfigurationManager;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ImportOrdersFromApi2cartJobTest extends TestCase
{
    public function test_if_job_runs_without_exceptions()
    {
        Event::fake();

        // we set key to api2cart demo store
        CompanyConfigurationManager::set('bridge_api_key', 'ed58a22dfecb405a50ea3ea56979360d');

        $job = new ImportOrdersFromApi2cartJob();

        $job->handle();

        // we just checking if there is no exceptions
        // this test only has to pass
        $this->assertTrue(true);
    }
}
