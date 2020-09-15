<?php

namespace Tests\Feature\Events;

use App\Events\Order\StatusChangedEvent;
use App\Models\Order;
use App\Observers\OrderObserver;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class OrderStatusChangedEventTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIfEventIsDispatchedWhenStatusChanged()
    {
        // prepare
        Event::fake();
        $observer = new OrderObserver();
        $order = factory(Order::class)->create([
            'status_code' => 'picking'
        ]);

        // act
        $order->status_code = 'test';
        $observer->updated($order);

//         assert
        Event::assertDispatched(StatusChangedEvent::class);
    }
}
