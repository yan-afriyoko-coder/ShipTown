<?php

namespace App\Modules\InventoryReservations\src\Listeners;

use App\Events\OrderProduct\OrderProductUpdatedEvent;
use App\Modules\InventoryReservations\src\Jobs\UpdateInventoryQuantityReservedJob;

class OrderProductUpdatedEventListener
{
    /**
     * Handle the event.
     *
     * @param OrderProductUpdatedEvent $event
     *
     */
    public function handle(OrderProductUpdatedEvent $event)
    {
        if ($event->orderProduct->product_id === null) {
            return;
        }

        UpdateInventoryQuantityReservedJob::dispatchNow($event->orderProduct->product_id);
    }
}
