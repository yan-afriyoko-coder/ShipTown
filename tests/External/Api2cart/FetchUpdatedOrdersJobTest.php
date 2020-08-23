<?php

namespace Tests\External\Api2cart;

use App\Jobs\Api2cart\ProcessApi2cartImportedOrderJob;
use App\Modules\Api2cart\src\Jobs\FetchUpdatedOrdersJob;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class FetchUpdatedOrdersJobTest extends TestCase
{
    public function testIfJobRunsWithoutExceptions()
    {
        $this->expectNotToPerformAssertions();

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

        $job = new FetchUpdatedOrdersJob($api2cartConnection);

        $job->handle();
    }
}
