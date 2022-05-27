<?php

namespace App\Observers;

use App\Events\Inventory\InventoryUpdatedEvent;
use App\Models\Inventory;

class InventoryObserver
{
    /**
     * Handle the inventory "updated" event.
     *
     * @param Inventory $inventory
     *
     * @return void
     */
    public function updated(Inventory $inventory)
    {
        if ($inventory->isAnyAttributeChanged(['quantity', 'quantity_reserved', 'shelve_location'])) {
            InventoryUpdatedEvent::dispatch($inventory);
        }
    }
}
