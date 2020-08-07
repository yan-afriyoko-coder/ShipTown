<?php

namespace Tests\Feature\Jobs\Orders;

use App\Jobs\Orders\RecalculateOrderProductLineCountJob;
use App\Models\Order;
use App\Models\OrderProduct;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RecalculateOrderProductLineCountJobTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_if_updates_correctly()
    {
        $order = factory(Order::class)->create();

        $orderProductsCount = rand(1,5);

        $order->orderProducts()->saveMany(
            factory(OrderProduct::class, $orderProductsCount)->make()
        );

        $order->product_line_count = $order->product_line_count + rand();

        RecalculateOrderProductLineCountJob::dispatchNow();

        $order = $order->refresh();

        $this->assertTrue($orderProductsCount, $order->product_line_count);
    }
}
