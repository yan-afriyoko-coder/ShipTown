<?php

namespace Tests\Feature\Events;

use App\Events\OrderStatusChangedEvent;
use App\Models\Order;
use App\Observers\OrderObserver;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class OrderStatusChangedEventTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_if_event_is_dispatched_when_status_changed()
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
        Event::assertDispatched(OrderStatusChangedEvent::class);
    }
}
