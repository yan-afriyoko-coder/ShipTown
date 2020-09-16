<?php

namespace App\Listeners\Order\StatusChanged;

use App\Events\Order\StatusChangedEvent;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class PackingWarehouseRule
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param StatusChangedEvent $event
     * @return void
     */
    public function handle(StatusChangedEvent $event)
    {
        // todo change hardcoded
        $expectedStatusCode = 'picking';

        if ($event->isNotStatusCode($expectedStatusCode)) {
            return;
        }

        $this->checkStatusAndUpdate($event->getOrder());
    }

    /**
     * @param Order $order
     */
    public function checkStatusAndUpdate(Order $order): void
    {
        // todo change hardcoded
        $sourceLocationId = 99;
        $newStatusCode = 'packing_warehouse';

        if (OrderService::canNotFulfill($order, $sourceLocationId)) {
            return;
        }

        $order->update(['status_code' => $newStatusCode]);

        // Log event
        info(
            'PackingWarehouseRule: set status to packing_warehouse',
            [
                'order_number' => $order->order_number,
                'source_location_id' => $sourceLocationId,
            ]
        );
    }
}
