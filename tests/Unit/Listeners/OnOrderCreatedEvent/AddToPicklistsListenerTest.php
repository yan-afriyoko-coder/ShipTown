<?php

namespace Tests\Feature;

use App\Events\OrderCreatedEvent;
use App\Listeners\OnOrderCreatedEvent\AddToPicklistsListener;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Picklist;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AddToPicklistsListenerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_if_picklist_is_populated()
    {
        Event::fake();

        Picklist::query()->delete();
        OrderProduct::query()->delete();
        Order::query()->delete();

        $order = factory(Order::class)->create();

        $order->orderProducts()->saveMany(
            factory(OrderProduct::class, 10)->make()
        );

        $listener = new AddToPicklistsListener();

        $listener->handle(new OrderCreatedEvent($order));

        $this->assertEquals(
            Picklist::query()->sum('quantity_to_pick'),
            OrderProduct::query()->sum('quantity')
        );
    }
}
