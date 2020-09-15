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
        $sourceLocationId = 99;

        if (OrderService::canNotFulfill($event->getOrder(), $sourceLocationId)) {
            logger('We cannot fulfill full order', [
                'order_number' => $event->getOrder()->order_number,
                'source_location_id' => $sourceLocationId,
            ]);
            return;
        }

        // todo change hardcoded status_code
        $event->getOrder()->update(['status_code' => 'packing_warehouse']);

        // Log event
        info(
            'Can fulfill from warehouse, set status to packing_warehouse',
            [
                'order_number' => $event->getOrder()->order_number,
                'source_location_id' => $sourceLocationId,
            ]
        );
    }
}
