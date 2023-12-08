<?php

namespace Database\Seeders;

use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Modules\InventoryMovements\src\Jobs\SequenceNumberJob;
use Illuminate\Database\Seeder;

class SalesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $inventoryMovements = Inventory::query()
            ->inRandomOrder()
            ->limit(20)
            ->get()
            ->map(function (Inventory $inventory) {
                $quantityDelta = min(rand(1, 7), $inventory->quantity) * (-1);

                return [
                    'occurred_at' => now(),
                    'type' => 'sale',
                    'inventory_id' => $inventory->id,
                    'product_id' => $inventory->product_id,
                    'warehouse_id' => $inventory->warehouse_id,
                    'quantity_before' => $inventory->quantity,
                    'quantity_delta' => $quantityDelta,
                    'quantity_after' => $inventory->quantity + $quantityDelta,
                    'description' => 'sale',
                ];
            });

        InventoryMovement::query()->insert($inventoryMovements->toArray());

        SequenceNumberJob::dispatch();
    }
}
