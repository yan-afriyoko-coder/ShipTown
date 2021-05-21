<?php

namespace App\Modules\InventoryReservations\src\Listeners;

use App\Events\Inventory\InventoryCreatedEvent;

class InventoryCreatedListener
{
    /**
     * Handle the event.
     *
     * @param InventoryCreatedEvent $event
     * @return void
     */
    public function handle(InventoryCreatedEvent $event)
    {
        $event->getInventory()->product
            ->recalculateQuantityTotals()
            ->save();
    }
}
