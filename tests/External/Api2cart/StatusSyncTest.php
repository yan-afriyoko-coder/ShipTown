<?php

namespace Tests\External\Api2cart;

use App\Models\Order;
use App\Modules\Api2cart\src\Api\Orders;
use App\Modules\Api2cart\src\Api\OrderStatus;
use App\Modules\Api2cart\src\Exceptions\RequestException;
use App\Modules\Api2cart\src\Jobs\DispatchImportOrdersJobs;
use App\Modules\Api2cart\src\Models\Api2cartConnection;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatusSyncTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     * @throws RequestException
     * @throws GuzzleException
     */
    public function test_if_syncs_status()
    {
        // we set key to api2cart demo store
        $api2cartConnection = new Api2cartConnection([
            'location_id'    => '99',
            'type'           => 'magento',
            'url'            => 'https://demo.api2cart.com/opencart',
            'bridge_api_key' => config('api2cart.api2cart_test_store_key'),
        ]);
        $api2cartConnection->save();

        DispatchImportOrdersJobs::dispatchNow();

        $response = OrderStatus::list($api2cartConnection->bridge_api_key);

        $orderStatusList = $response->getResult()['cart_order_statuses'];

        $randomStatus = $orderStatusList[rand(0, count($orderStatusList)-1)];

        \App\Models\OrderStatus::firstOrCreate([
            'name' => $randomStatus['id'],
            'code' => $randomStatus['id'],
            'sync_ecommerce' => true,
        ]);

        $order = Order::first();

        $order->status_code = $randomStatus['id'];
        $order->save();

        $response = Orders::list($api2cartConnection->bridge_api_key, ['ids' => $order->order_number]);

        $this->assertEquals($order->status_code, $response->getResult()['order'][0]['status']['id']);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     * @throws RequestException
     * @throws GuzzleException
     */
    public function test_if_not_syncs_status_when_disabled()
    {
        // we set key to api2cart demo store
        $api2cartConnection = new Api2cartConnection([
            'location_id'    => '99',
            'type'           => 'magento',
            'url'            => 'https://demo.api2cart.com/opencart',
            'bridge_api_key' => config('api2cart.api2cart_test_store_key'),
        ]);
        $api2cartConnection->save();

        DispatchImportOrdersJobs::dispatchNow();

        $response = OrderStatus::list($api2cartConnection->bridge_api_key);

        $orderStatusList = $response->getResult()['cart_order_statuses'];

        $randomStatus = $orderStatusList[rand(0, count($orderStatusList)-1)];

        \App\Models\OrderStatus::updateOrCreate([
                'code' => $randomStatus['id']
            ], [
                'name' => $randomStatus['id'],
                'sync_ecommerce' => false,
            ]);

        $order = Order::first();

        $order->status_code = $randomStatus['id'];
        $order->save();

        $response = Orders::list($api2cartConnection->bridge_api_key, ['ids' => $order->order_number]);

        $this->assertNotEquals($order->status_code, $response->getResult()['order'][0]['status']['id']);
    }
}
