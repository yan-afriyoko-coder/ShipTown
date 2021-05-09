<?php

namespace Tests\External\Api2cart\Jobs;

use App\Modules\Api2cart\src\Exceptions\RequestException;
use App\Modules\Api2cart\src\Jobs\FetchUpdatedProductsJob;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use Tests\TestCase;

class FetchUpdatedProductsJobTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     * @throws RequestException
     */
    public function testExample()
    {
        // we set key to api2cart demo store
        new Api2cartConnection([
            'location_id' => '99',
            'type' => 'opencart',
            'url' => 'http://demo.api2cart.com/opencart',
            'bridge_api_key' => env('API2CART_TEST_STORE_KEY')
        ]);

        $job = new FetchUpdatedProductsJob();

        $job->handle();

        // we just want to make sure there is no exceptions
        $this->assertTrue(true);
    }
}
