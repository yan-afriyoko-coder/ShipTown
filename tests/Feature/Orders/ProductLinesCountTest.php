<?php

namespace Tests\Feature\Orders;

use App\Models\Order;
use App\Models\OrderProduct;
use Tests\TestCase;

class ProductLinesCountTest extends TestCase
{
    public function testIfProductLineCountIsPopulatedCorrectly()
    {
        /** @var Order $order */
        $order = factory(Order::class)->create();

        $orderProductCount = rand(1, 5);

        $order->orderProducts()->saveMany(
            factory(OrderProduct::class, $orderProductCount)->make()
        );

        $order = $order->refresh();

        $this->assertEquals($order->orderTotals->product_line_count, $orderProductCount);
    }
}
