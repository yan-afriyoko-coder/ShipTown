<?php

namespace App\Observers;

use App\Events\Inventory\InventoryCreatedEvent;
use App\Events\Inventory\InventoryUpdatedEvent;
use App\Models\Inventory;

class InventoryObserver
{
    /**
     * Handle the inventory "created" event.
     *
     * @param Inventory $inventory
     *
     * @return void
     */
    public function created(Inventory $inventory)
    {
        $inventory->product->recalculateQuantityTotals()->save();
        InventoryCreatedEvent::dispatch($inventory);
    }

    /**
     * Handle the inventory "updated" event.
     *
     * @param Inventory $inventory
     *
     * @return void
     */
    public function updated(Inventory $inventory)
    {
        if ($inventory->isAnyAttributeChanged(['quantity', 'quantity_reserved'])) {
            $inventory->product->log('updated inventory', [
                'warehouse' => $inventory->warehouse ? $inventory->warehouse->code : '',
                'quantity' => $inventory->quantity,
                'reserved' => $inventory->quantity_reserved,
                'available' => $inventory->quantity_available,
            ]);
            $inventory->product->recalculateQuantityTotals()->save();
        }

        if ($inventory->isAnyAttributeChanged(['quantity', 'quantity_reserved', 'shelve_location'])) {
            InventoryUpdatedEvent::dispatch($inventory);
        }
    }
}
