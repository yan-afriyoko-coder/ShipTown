<?php

namespace Tests\Feature\Orders;

use App\Models\Order;
use App\Models\RmsapiProductImport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateOrderClosedAtTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIfDoesNotFillClosedAtWhenCompletedStatus()
    {
        RmsapiProductImport::query()->forceDelete();
        Order::query()->forceDelete();

        $order = factory(Order::class)->create([
            'status_code' => 'blue'
        ]);

        $this->assertNull($order->order_closed_at);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIfFillsClosedAtWhenCompletedStatus()
    {
        RmsapiProductImport::query()->forceDelete();
        Order::query()->forceDelete();

        $order = factory(Order::class)->create([
            'status_code' => 'complete'
        ]);

        $this->assertNotNull($order->order_closed_at);
    }
}
