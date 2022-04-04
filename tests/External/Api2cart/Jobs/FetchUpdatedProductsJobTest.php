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
     * @throws RequestException
     *
     * @return void
     */
    public function testExample()
    {
        // we set key to api2cart demo store
        new Api2cartConnection([
            'location_id'    => '99',
            'type'           => 'opencart',
            'url'            => 'http://demo.api2cart.com/opencart',
            'bridge_api_key' => config('api2cart.api2cart_test_store_key'),
            'inventory_source_warehouse_tag' => 'magento_stock'
        ]);

        $job = new FetchUpdatedProductsJob();

        $job->handle();

        // we just want to make sure there is no exceptions
        $this->assertTrue(true);
    }
}
