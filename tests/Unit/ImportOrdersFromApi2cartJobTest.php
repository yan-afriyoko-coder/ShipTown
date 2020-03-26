<?php

namespace Tests\Unit;

use App\Jobs\ImportOrdersFromApi2cartJob;
use App\Managers\CompanyConfigurationManager;
use App\Managers\Config;
use App\User;
use Laravel\Passport\Passport;
use Mockery\Generator\StringManipulation\Pass\Pass;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ImportOrdersFromApi2cartJobTest extends TestCase
{
    public function test_if_job_runs_without_exceptions()
    {
        // we set key to api2cart demo store
        CompanyConfigurationManager::set('bridge_api_key', 'ed58a22dfecb405a50ea3ea56979360d');

//        Config::setApi2cartStoreKey('');

        $job = new ImportOrdersFromApi2cartJob();

        $job->handle();

        // we just checking if there is no exceptions
        // this test only has to pass
        $this->assertTrue(true);
    }
}
