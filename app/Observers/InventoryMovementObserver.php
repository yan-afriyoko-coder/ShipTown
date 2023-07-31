<?php

namespace App\Observers;

use App\Events\InventoryMovement\InventoryMovementCreatedEvent;
use App\Models\InventoryMovement;

class InventoryMovementObserver
{
    public function created(InventoryMovement $inventoryMovement)
    {
        InventoryMovementCreatedEvent::dispatch($inventoryMovement);
    }
}
