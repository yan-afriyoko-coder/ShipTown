<?php

namespace Tests\Unit\Listeners;

use App\Events\Order\StatusChangedEvent;
use App\Listeners\Order\StatusChanged\AddToOldPicklistListener;
use App\Listeners\Order\StatusChanged\RemoveFromOldPicklistListener;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Picklist;
use App\Services\PicklistService;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class RemoveFromPicklistListenerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIfPicklistIsPopulated()
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
        (new AddToOldPicklistListener())
            ->handle(new StatusChangedEvent($order));

        (new RemoveFromOldPicklistListener())
            ->handle(new StatusChangedEvent($order));

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
