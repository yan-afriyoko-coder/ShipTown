<?php

namespace Tests\External\Api2cart;

use App\Jobs\Api2cart\ImportOrdersJob;
use App\Jobs\Api2cart\ProcessImportedOrdersJob;
use App\Models\Api2cartConnection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ImportOrdersJobTest extends TestCase
{
    public function test_if_job_runs_without_exceptions()
    {
        Bus::fake();
        Event::fake();

        // we want to test on clean data
        Api2cartConnection::query()->delete();

        // we set key to api2cart demo store
        $api2cartConnection = new Api2cartConnection([
            'location_id' => '99',
            'type' => 'opencart',
            'url' => 'http://demo.api2cart.com/opencart',
            'bridge_api_key' => env('API2CART_TEST_STORE_KEY')
        ]);

        $api2cartConnection->save();

        $job = new ImportOrdersJob($api2cartConnection);

        $job->handle();

        Bus::assertDispatched(ProcessImportedOrdersJob::class);
    }
}
