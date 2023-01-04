<?php

namespace App\Observers;

use App\Events\Inventory\InventoryUpdatedEvent;
use App\Models\Inventory;

class InventoryObserver
{
    public function updating(Inventory $inventory)
    {
        if ($inventory->quantity > $inventory->getOriginal('quantity')) {
            $inventory->last_received_at = now();

            if (! $inventory->first_received_at) {
                $inventory->first_received_at = now();
            }
        }
    }

    public function updated(Inventory $inventory)
    {
        InventoryUpdatedEvent::dispatch($inventory);
    }
}
