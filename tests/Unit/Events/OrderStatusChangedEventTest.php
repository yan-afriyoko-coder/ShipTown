<?php

namespace Tests\Unit\Events;

use App\Events\OrderStatusChangedEvent;
use App\Models\Order;
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
        Event::fake();

        $order = factory(Order::class)->create();

        $order->status_code = 'test';

        $order->save();

        Event::assertDispatched(OrderStatusChangedEvent::class);
    }
}
