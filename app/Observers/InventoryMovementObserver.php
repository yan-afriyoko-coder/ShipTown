<?php

namespace App\Observers;

use App\Events\InventoryMovementCreatedEvent;
use App\Models\InventoryMovement;

class InventoryMovementObserver
{
    public function created(InventoryMovement $inventoryMovement)
    {
        InventoryMovementCreatedEvent::dispatch($inventoryMovement);
    }
}
