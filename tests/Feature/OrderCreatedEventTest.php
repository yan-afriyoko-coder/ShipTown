<?php

namespace Tests\Feature;

use App\Events\OrderCreatedEvent;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Picklist;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderCreatedEventTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        // clean up database
        Order::query()->delete();
        OrderProduct::query()->delete();
        Picklist::query()->delete();

        // make sure everything is empty
        $this->assertEquals(0, Order::query()->count());
        $this->assertEquals(0, OrderProduct::query()->count());
        $this->assertEquals(0, Picklist::query()->count());

        $order = factory(Order::class)->create([
            'status_code' => 'picking'
        ]);

        $order->orderProducts()->saveMany(factory(OrderProduct::class, 10)->make());


        // act
        OrderCreatedEvent::dispatch($order);

        // check if all quantities are added to picklist
        $this->assertEquals(
            Picklist::query()->sum('quantity_requested'),
            Order::query()->first()->orderProducts()->sum('quantity'),
        );

    }
}
