<?php

namespace Tests\Feature\Orders;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Pick;
use App\Models\PickRequest;
use Tests\TestCase;

class PickRequestsIntegrationTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIfRemovesPickRequestsCorrectly()
    {
        OrderProduct::query()->forceDelete();
        Order::query()->forceDelete();
        PickRequest::query()->forceDelete();
        Pick::query()->forceDelete();

        $order = factory(Order::class)
            ->with('orderProducts')
            ->create(['status_code' => 'processing']);

        $order->update(['status_code' => 'picking']);
        $order->update(['status_code' => 'processing']);

        $this->assertEquals(
            0,
            Pick::query()->sum('quantity_required')
        );

        $this->assertEquals(
            1,
            PickRequest::onlyTrashed()->count()
        );

        $this->assertFalse(
            PickRequest::query()->exists()
        );
    }
}
