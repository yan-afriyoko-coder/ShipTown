<?php

namespace App\Modules\InventoryReservations\src\Listeners;

use App\Events\OrderProduct\OrderProductCreatedEvent;
use App\Modules\InventoryReservations\src\Jobs\UpdateInventoryQuantityReservedJob;

class OrderProductCreatedEventListener
{
    /**
     * Handle the event.
     *
     * @param OrderProductCreatedEvent $event
     *
     * @return void
     */
    public function handle(OrderProductCreatedEvent $event)
    {
        if ($event->orderProduct->product_id === null) {
            return;
        }

        UpdateInventoryQuantityReservedJob::dispatchSync($event->orderProduct->product_id);
    }
}
