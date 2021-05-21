<?php

namespace App\Modules\InventoryReservations\src\Listeners;

use App\Events\Inventory\InventoryUpdatedEvent;

class InventoryUpdatedListener
{
    /**
     * Handle the event.
     *
     * @param InventoryUpdatedEvent $event
     * @return void
     */
    public function handle(InventoryUpdatedEvent $event)
    {
        $event->getInventory()->product
            ->recalculateQuantityTotals()
            ->save();
    }
}
