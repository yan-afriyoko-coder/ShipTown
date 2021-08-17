<?php

namespace Tests\Feature\Modules\Automations\Listeners;

use App\Events\Order\OrderCreatedEvent;
use App\Events\Order\OrderUpdatedEvent;
use App\Models\Inventory;
use App\Models\Order;
use App\Modules\Automations\src\Listeners\EventsListener;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class EventsListenerTest extends TestCase
{
    use RefreshDatabase;

    private $listener;

    protected function setUp(): void
    {
        parent::setUp();

        $this->listener = Mockery::mock(EventsListener::class);
        app()->instance(EventsListener::class, $this->listener);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_order_created_event()
    {
        $this->listener->shouldReceive('handle');

        /** @var Order $order */
        $order = factory(Order::class)->create();

        OrderCreatedEvent::dispatch($order);

        $this->listener->shouldHaveReceived('handle', [OrderCreatedEvent::class]);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_order_updated_event()
    {
        $this->listener->shouldReceive('handle');

        /** @var Order $order */
        $order = factory(Order::class)->create(['status_code' => 'paid']);

        $order->status_code = 'new_status';
        $order->save();

        $this->listener->shouldHaveReceived('handle', [OrderUpdatedEvent::class]);
    }
}
