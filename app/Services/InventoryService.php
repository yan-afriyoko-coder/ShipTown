<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    public static function sellProduct(Inventory $inventory, float $quantityDelta, string $description, string $unique_reference_id = null): InventoryMovement
    {
        return DB::transaction(function () use ($inventory, $quantityDelta, $description, $unique_reference_id) {
            /** @var InventoryMovement $inventoryMovement */
            $inventoryMovement = InventoryMovement::query()->create([
                'custom_unique_reference_id' => $unique_reference_id,
                'type' => 'sale',
                'inventory_id' => $inventory->id,
                'product_id' => $inventory->product_id,
                'warehouse_id' => $inventory->warehouse_id,
                'quantity_before' => $inventory->quantity,
                'quantity_delta' => $quantityDelta,
                'quantity_after' => $inventory->quantity + $quantityDelta,
                'description' => $description,
            ]);

            $inventory->update([
                'quantity' => $inventoryMovement->quantity_after,
                'last_movement_at' => $inventoryMovement->created_at,
                'last_movement_id' => $inventoryMovement->id,
            ]);

            return $inventoryMovement;
        });
    }

    public static function adjustQuantity(Inventory $inventory, float $quantityDelta, string $description, string $unique_reference_id = null): InventoryMovement
    {
        return DB::transaction(function () use ($inventory, $quantityDelta, $description, $unique_reference_id) {
            /** @var InventoryMovement $inventoryMovement */
            $inventoryMovement = InventoryMovement::query()->create([
                'custom_unique_reference_id' => $unique_reference_id,
                'type' => 'manual_adjustment',
                'inventory_id' => $inventory->id,
                'product_id' => $inventory->product_id,
                'warehouse_id' => $inventory->warehouse_id,
                'quantity_before' => $inventory->quantity,
                'quantity_delta' => $quantityDelta,
                'quantity_after' => $inventory->quantity + $quantityDelta,
                'description' => $description,
            ]);

            $inventory->update([
                'quantity' => $inventoryMovement->quantity_after,
                'last_movement_at' => $inventoryMovement->created_at,
                'last_movement_id' => $inventoryMovement->id
            ]);

            return $inventoryMovement;
        });
    }

    public static function stocktake(Inventory $inventory, float $newQuantity, string $unique_reference_id = null): InventoryMovement
    {
        return DB::transaction(function () use ($inventory, $newQuantity, $unique_reference_id) {
            /** @var InventoryMovement $inventoryMovement */
            $inventoryMovement = InventoryMovement::query()->create([
                'custom_unique_reference_id' => $unique_reference_id,
                'type' => 'stocktake',
                'description' => 'stocktake',
                'inventory_id' => $inventory->id,
                'product_id' => $inventory->product_id,
                'warehouse_id' => $inventory->warehouse_id,
                'quantity_before' => $inventory->quantity,
                'quantity_delta' => $newQuantity - $inventory->quantity,
                'quantity_after' => $newQuantity,
            ]);

            $inventory->update([
                'quantity' => $inventoryMovement->quantity_after,
                'last_movement_at' => $inventoryMovement->created_at,
                'last_movement_id' => $inventoryMovement->id,
                'last_counted_at' => $inventoryMovement->created_at
            ]);

            return $inventoryMovement;
        });
    }
}
