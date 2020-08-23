<?php

namespace Tests\Feature\Orders;

use App\Models\Order;
use App\Models\OrderProduct;
use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductLinesCountTest extends TestCase
{
    public function testIfProductLineCountIsPopulatedCorrectly()
    {
        $order = factory(Order::class)->create();

        $orderProductCount = rand(1, 5);

        $order->orderProducts()->saveMany(
            factory(OrderProduct::class, $orderProductCount)->make()
        );

        $order = $order->refresh();

        $this->assertEquals($order->product_line_count, $orderProductCount);
    }
}
