<?php

namespace Tests\Unit\Listeners;

use App\Events\Order\CreatedEvent;
use App\Listeners\Order\Created\AddToOldPicklistListener;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Picklist;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class AddToPicklistsListenerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIfPicklistIsPopulated()
    {
        Event::fake();

        Picklist::query()->delete();
        OrderProduct::query()->delete();
        Order::query()->delete();

        $order = factory(Order::class)->create([
            'status_code' => 'picking'
        ]);

        $order->orderProducts()->saveMany(
            factory(OrderProduct::class, 10)->make()
        );

        $listener = new AddToOldPicklistListener();

        $listener->handle(new CreatedEvent($order));

        $this->assertEquals(
            Picklist::query()->sum('quantity_requested'),
            OrderProduct::query()->sum('quantity_ordered')
        );
    }
}
