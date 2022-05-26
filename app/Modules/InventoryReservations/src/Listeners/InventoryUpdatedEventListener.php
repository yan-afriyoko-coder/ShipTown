<?php

namespace App\Modules\InventoryReservations\src\Listeners;

use App\Events\Inventory\InventoryUpdatedEvent;
use App\Modules\InventoryReservations\src\Jobs\UpdateProductQuantityReservedJob;

/**
 *
 */
class InventoryUpdatedEventListener
{
    /**
     * @param InventoryUpdatedEvent $event
     */
    public function handle(InventoryUpdatedEvent $event)
    {
        if ($event->inventory->isAttributeNotChanged('quantity_reserved')) {
            return;
        }

        UpdateProductQuantityReservedJob::dispatchNow($event->inventory->product_id);
    }
}
