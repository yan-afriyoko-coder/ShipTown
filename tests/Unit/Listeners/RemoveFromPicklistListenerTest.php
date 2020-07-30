<?php

namespace Tests\Unit\Listeners;

use App\Events\OrderCreatedEvent;
use App\Events\OrderStatusChangedEvent;
use App\Listeners\AddToPicklistOnOrderCreatedEventListener;
use App\Listeners\OrderStatusChangedListener;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Picklist;
use App\Services\PicklistService;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RemoveFromPicklistListenerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_if_picklist_is_populated()
    {
        // prepare
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

        PicklistService::addOrderProductPick(
            $order->orderProducts()->get()->toArray()
        );

        $order->update(['status_code' => 'processing']);

        // act
        (new OrderStatusChangedListener())
            ->handle(new OrderStatusChangedEvent($order));

        // assert
        $this->assertEquals(
            0,
            Picklist::query()
                ->whereNull('picked_at')
                ->whereNull('deleted_at')
                ->sum('quantity_requested')
        );
    }
}
