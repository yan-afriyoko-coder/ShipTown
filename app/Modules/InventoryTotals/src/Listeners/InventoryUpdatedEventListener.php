<?php

namespace App\Modules\InventoryTotals\src\Listeners;

use App\Events\Inventory\InventoryUpdatedEvent;
use App\Models\Inventory;
use App\Models\InventoryTotal;
use App\Models\Product;

/**
 *
 */
class InventoryUpdatedEventListener
{
    /**
     * @param InventoryUpdatedEvent $event
     */
    public function handle(InventoryUpdatedEvent $event)
    {
        $totals = Inventory::query()
            ->selectRaw('
                SUM(quantity) as quantity,
                SUM(quantity_reserved) as quantity_reserved,
                SUM(quantity_incoming) as quantity_incoming
            ')
            ->where(['product_id' => $event->inventory->product_id])
            ->first();

        InventoryTotal::query()->updateOrCreate([
            'product_id' => $event->inventory->product_id
        ], [
            'quantity' => $totals->quantity,
            'quantity_reserved' => $totals->quantity_reserved,
            'quantity_incoming' => $totals->quantity_incoming,
            'updated_at' => now(),
        ]);

        Product::query()->where([
                'id' => $event->inventory->product_id
            ])->update([
                'quantity' => $totals->quantity,
                'quantity_reserved' => $totals->quantity_reserved,
                'updated_at' => now(),
            ]);
    }
}
