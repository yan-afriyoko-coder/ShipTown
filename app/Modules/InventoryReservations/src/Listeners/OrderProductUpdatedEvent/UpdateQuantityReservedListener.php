<?php

namespace App\Modules\InventoryReservations\src\Listeners\OrderProductUpdatedEvent;

use App\Events\OrderProduct\OrderProductUpdatedEvent;
use App\Modules\InventoryReservations\src\Jobs\UpdateQuantityReservedJob;

class UpdateQuantityReservedListener
{
    /**
     * Handle the event.
     *
     * @param OrderProductUpdatedEvent $event
     *
     * @return bool
     */
    public function handle(OrderProductUpdatedEvent $event): bool
    {
        if ($event->orderProduct->product_id === null) {
            return true;
        }

        UpdateQuantityReservedJob::dispatch($event->orderProduct->product_id);

        return true;
    }
}
