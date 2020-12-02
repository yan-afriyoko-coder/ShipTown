<?php

namespace Tests\Feature\Jobs\Orders;

use App\Jobs\Orders\RecalculateProductLineCountJob;
use App\Models\Order;
use App\Models\OrderProduct;
use Tests\TestCase;

class RecalculateOrderProductLineCountJobTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIfUpdatesCorrectly()
    {
        $order = factory(Order::class)->create();

        $orderProductsCount = rand(1, 5);

        $order->orderProducts()->saveMany(
            factory(OrderProduct::class, $orderProductsCount)->make()
        );

        $order->product_line_count = $order->product_line_count + rand();
        $order->save();

        RecalculateProductLineCountJob::dispatchNow();

        $order = $order->refresh();

        $this->assertEquals($orderProductsCount, $order->product_line_count);
    }
}
