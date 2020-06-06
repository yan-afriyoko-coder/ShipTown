<?php

namespace Tests\External;

use App\Jobs\Api2cart\ImportOrdersFromApi2cartJob;
use App\Managers\CompanyConfigurationManager;
use App\User;
use Illuminate\Support\Facades\Bus;
use Laravel\Passport\Passport;
use Tests\TestCase;

class ImportRoutesTest extends TestCase
{
    public function test_if_import_from_api2cart_route_works()
    {
        Bus::fake();

        Passport::actingAs(
            factory(User::class)->create()
        );

        $response = $this->get('api/import/orders/from/api2cart');

        $response->assertOk();

        Bus::assertDispatched(ImportOrdersFromApi2cartJob::class);
    }

    public function test_if_import_job_runs_correctly() {

        // we set key to api2cart demo store
        CompanyConfigurationManager::set('bridge_api_key', env('API2CART_TEST_STORE_KEY'));

        $job = new ImportOrdersFromApi2cartJob();

        $job->handle();

        $this->assertTrue($job->finishedSuccessfully);
    }
}
