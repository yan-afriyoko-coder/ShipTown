<?php

namespace App\Modules\InventoryReservations\src\Listeners;

use App\Events\Inventory\InventoryUpdatedEvent;
use App\Modules\InventoryReservations\src\Jobs\UpdateProductQuantityJob;
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
        if ($event->inventory->isAnyAttributeChanged(['quantity', 'quantity_reserved'])) {
            return;
        }

        UpdateProductQuantityJob::dispatchNow($event->inventory->product_id);
        UpdateProductQuantityReservedJob::dispatchNow($event->inventory->product_id);
    }
}
