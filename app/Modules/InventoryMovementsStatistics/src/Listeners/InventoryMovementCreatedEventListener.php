<?php

namespace App\Modules\InventoryMovementsStatistics\src\Listeners;

use App\Events\InventoryMovementCreatedEvent;
use Illuminate\Support\Facades\DB;

class InventoryMovementCreatedEventListener
{
    public function handle(InventoryMovementCreatedEvent $event)
    {
        if ($event->inventoryMovement->type === 'sale') {
            DB::table('modules_inventory_movements_statistics_last28days_sale_movements')->insert([
                'inventory_movement_id' => $event->inventoryMovement->id,
                'sold_at' => $event->inventoryMovement->created_at,
                'inventory_id' => $event->inventoryMovement->inventory_id,
                'warehouse_id' => $event->inventoryMovement->warehouse_id,
                'quantity_sold' => $event->inventoryMovement->quantity_delta * -1
            ]);
        }
    }
}
