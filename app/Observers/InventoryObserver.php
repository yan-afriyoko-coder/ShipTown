<?php

namespace App\Observers;

use App\Events\Inventory\InventoryCreatedEvent;
use App\Events\Inventory\InventoryUpdatedEvent;
use App\Events\ProductPrice\ProductPriceUpdatedEvent;
use App\Models\Inventory;

class InventoryObserver
{
    /**
     * Handle the inventory "created" event.
     *
     * @param Inventory $inventory
     * @return void
     */
    public function created(Inventory $inventory)
    {
        InventoryCreatedEvent::dispatch($inventory);
    }

    /**
     * Handle the inventory "updated" event.
     *
     * @param Inventory $inventory
     * @return void
     */
    public function updated(Inventory $inventory)
    {
        $changed = $inventory->isAnyAttributeChanged([
            'quantity',
            'quantity_reserved',
            'shelve_location',
        ]);

        if ($changed) {
            InventoryUpdatedEvent::dispatch($inventory);
        }
    }
}
