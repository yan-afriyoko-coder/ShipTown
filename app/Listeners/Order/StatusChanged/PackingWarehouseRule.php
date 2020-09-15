<?php

namespace App\Listeners\Order\StatusChanged;

use App\Events\Order\StatusChangedEvent;
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
        if ($event->isNotStatusCode('picking')) {
            return;
        }

        // todo change hardcoded sourceLocationId
        if (OrderService::canNotFulfill($event->getOrder(), 99)) {
            return;
        }

        // todo change hardcoded status_code
        $event->getOrder()->update(['status_code' => 'packing_warehouse']);

        // Log event
        info(
            'Order can be fulfilled from warehouse, changing status to packing_warehouse >> only testing, not actually changed',
            ['order_number' => $event->getOrder()->order_number]
        );
    }
}
