<?php

namespace App\Services;

use App\Models\Inventory;
use App\Models\InventoryMovement;

class InventoryService
{
    public static function sell(Inventory $inventory, float $quantityDelta, array $attributes = null): InventoryMovement
    {
        $movement = InventoryService::adjust($inventory, $quantityDelta, ['type' => InventoryMovement::TYPE_SALE]);

        $movement->fill($attributes ?? []);
        $movement->save();

        return $movement;
    }

    public static function stocktake(Inventory $inventory, float $newQuantity, array $attributes = null): InventoryMovement
    {
        $movement = InventoryService::adjust($inventory, $newQuantity - $inventory->quantity, [
            'type' => InventoryMovement::TYPE_STOCKTAKE,
            'quantity_after' => $newQuantity,
            'description' => 'stocktake',
        ]);

        $movement->fill($attributes ?? []);
        $movement->save();

        return $movement;
    }

    public static function adjust(Inventory $inventory, float $quantityDelta, array $attributes = null): InventoryMovement
    {
        $inventoryRefreshed = $inventory->refresh();

        $inventoryMovement = new InventoryMovement([
            'occurred_at' => now(),
            'type' => InventoryMovement::TYPE_ADJUSTMENT,
            'inventory_id' => $inventoryRefreshed->id,
            'product_id' => $inventoryRefreshed->product_id,
            'warehouse_id' => $inventoryRefreshed->warehouse_id,
            'quantity_before' => $inventoryRefreshed->quantity,
            'quantity_delta' => $quantityDelta,
            'quantity_after' => $inventoryRefreshed->quantity + $quantityDelta,
            'description' => '',
        ]);

        $inventoryMovement->fill($attributes ?? []);
        $inventoryMovement->save();

        return $inventoryMovement->refresh();
    }
}
