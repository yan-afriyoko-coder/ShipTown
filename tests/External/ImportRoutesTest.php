<?php

namespace Tests\External;

use App\Jobs\Api2cart\ImportOrdersJob;
use App\Managers\CompanyConfigurationManager;
use App\Models\Api2cartConnection;
use App\User;
use Illuminate\Support\Facades\Bus;
use Laravel\Passport\Passport;
use Tests\TestCase;

class ImportRoutesTest extends TestCase
{
    public function test_if_import_job_runs_correctly() {

        // we set key to api2cart demo store
        Api2cartConnection::query()->delete();

        // we set key to api2cart demo store
        Api2cartConnection::query()->create([
            'bridge_api_key' => env('API2CART_TEST_STORE_KEY')
        ]);

        $job = new ImportOrdersJob();

        $job->handle();

        $this->assertTrue($job->finishedSuccessfully);
    }
}
