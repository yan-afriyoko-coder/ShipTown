<?php

namespace Tests\Feature\Modules\OrderStatus;

use App\Models\Order;
use App\Models\OrderStatus;
use App\Modules\OrderStatus\src\OrderStatusServiceProvider;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    public function test_if_order_is_active_updates()
    {
        /** @var OrderStatus $orderStatus */
        $orderStatus = factory(OrderStatus::class)->create(['order_active' => true]);

        factory(Order::class)->create(['status_code' => $orderStatus->code]);

        $this->assertDatabaseHas('orders', ['is_active' => true]);

        $orderStatus->update(['order_active' => false]);

        $this->assertDatabaseHas('orders', ['is_active' => false]);
    }

    public function test_if_order_on_hold_updates()
    {
        /** @var OrderStatus $orderStatus */
        $orderStatus = factory(OrderStatus::class)->create(['order_on_hold' => true]);

        factory(Order::class)->create(['status_code' => $orderStatus->code, 'is_on_hold' => true]);

        $this->assertDatabaseHas('orders', ['is_on_hold' => true]);

        $orderStatus->update(['order_on_hold' => false]);

        $this->assertDatabaseHas('orders', ['is_on_hold' => false]);
    }

    /** @test */
    public function test_if_can_enable()
    {
        OrderStatusServiceProvider::enableModule();

        $this->assertTrue(true, 'Most basic test... to be continued');
    }
}
