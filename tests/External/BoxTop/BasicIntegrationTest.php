<?php

namespace Tests\External\BoxTop;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Modules\BoxTop\src\Api\ApiClient;
use App\Modules\BoxTop\src\Services\BoxTopService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BasicIntegrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_successful_order_to_pick_integration()
    {
        /** @var Order $order */
        $order = factory(Order::class)->create();
        $order->shippingAddress->company = "BoxTop Technologies Ltd";
        $order->shippingAddress->full_name = "Reece Ipient";
        $order->shippingAddress->address1 = "Providence House";
        $order->shippingAddress->address2 = "2 River Street";
        $order->shippingAddress->city = "Windsor";
        $order->shippingAddress->state_code = "Berkshire";
        $order->shippingAddress->postcode = "SL4 1QT";
        $order->shippingAddress->country_code = "GB";
        $order->shippingAddress->phone = "+44 20 8400 2000";
        $order->shippingAddress->save();

        // we will pick one random product in stock
        $randomProduct = (new ApiClient())->getStockCheckByWarehouse()
            ->toCollection()
            ->random(1)
            ->first();

        factory(OrderProduct::class)->create([
            'order_id' => $order->getKey(),
            'sku_ordered' => $randomProduct['SKUNumber'],
            'name_ordered' => $randomProduct['SKUName'],
            'quantity_ordered' => 1,
        ]);

        $result = BoxTopService::postOrder($order, $randomProduct['Warehouse']);

        // well... it will be failing for the moment... so its... just ok....
        $this->assertTrue($result->http_response->getStatusCode() == 201);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        $boxtop = new ApiClient();

        $apiResponse = $boxtop->getAllProducts();

        $this->assertEquals(200, $apiResponse->http_response->getStatusCode());
    }
}
