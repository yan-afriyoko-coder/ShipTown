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
        $expectedStatusCode = 'picking';
        $sourceLocationId = 99;
        $newStatusCode = 'packing_warehouse';

        if ($event->isNotStatusCode($expectedStatusCode)) {
            return;
        }

        if (OrderService::canNotFulfill($event->getOrder(), $sourceLocationId)) {
            return;
        }

        $event->getOrder()->update(['status_code' => $newStatusCode]);

        // Log event
        info(
            'PackingWarehouseRule: set status to packing_warehouse',
            [
                'order_number' => $event->getOrder()->order_number,
                'source_location_id' => $sourceLocationId,
            ]
        );
    }
}
