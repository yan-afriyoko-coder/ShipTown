<?php

namespace App\Modules\InventoryMovementsStatistics\src\Listeners;

use App\Events\InventoryMovementCreatedEvent;
use App\Modules\InventoryMovementsStatistics\src\Models\InventoryMovementsStatistic;
use Illuminate\Support\Facades\DB;

class InventoryMovementCreatedEventListener
{
    public function handle(InventoryMovementCreatedEvent $event)
    {
        $inventoryMovement = $event->inventoryMovement;

        if ($inventoryMovement->type === 'sale') {
            DB::table('modules_inventory_movements_statistics_last28days_sale_movements')->insert([
                'inventory_movement_id' => $inventoryMovement->id,
                'sold_at' => $inventoryMovement->created_at,
                'inventory_id' => $inventoryMovement->inventory_id,
                'warehouse_id' => $inventoryMovement->warehouse_id,
                'quantity_sold' => $inventoryMovement->quantity_delta * -1
            ]);

            $recordsUpdated = InventoryMovementsStatistic::query()
                ->where('inventory_id', $inventoryMovement->inventory_id)
                ->update([
                    'quantity_sold_last_7_days' => DB::raw('IFNULL(quantity_sold_last_7_days, 0) + ' . $inventoryMovement->quantity_delta * -1),
                    'quantity_sold_last_14_days' => DB::raw('IFNULL(quantity_sold_last_14_days, 0) + ' . $inventoryMovement->quantity_delta * -1),
                    'quantity_sold_last_28_days' => DB::raw('IFNULL(quantity_sold_last_28_days, 0) + ' . $inventoryMovement->quantity_delta * -1),
                ]);

            if ($recordsUpdated === 0) {
                InventoryMovementsStatistic::query()
                    ->create([
                        'inventory_id'   => $inventoryMovement->inventory_id,
                        'product_id'     => $inventoryMovement->product_id,
                        'warehouse_id'   => $inventoryMovement->warehouse_id,
                        'warehouse_code' => $inventoryMovement->warehouse->code,
                        'quantity_sold_last_7days' => $inventoryMovement->quantity_delta * -1,
                        'quantity_sold_last_14days' => $inventoryMovement->quantity_delta * -1,
                        'quantity_sold_last_28days' => $inventoryMovement->quantity_delta * -1,
                    ]);
            }
        }
    }
}
