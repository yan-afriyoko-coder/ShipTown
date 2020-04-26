<?php

namespace Tests\External;

use App\Jobs\ImportOrdersFromApi2cartJob;
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
        CompanyConfigurationManager::set('bridge_api_key', 'ed58a22dfecb405a50ea3ea56979360d');

        $job = new ImportOrdersFromApi2cartJob();

        $job->handle();

        $this->assertTrue($job->finishedSuccessfully);
    }
}
