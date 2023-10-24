<?php

namespace App\Modules\InventoryMovements\src\Listeners;

use App\Events\InventoryMovement\InventoryMovementCreatedEvent;
use App\Models\InventoryMovement;
use Carbon\Carbon;

class InventoryMovementCreatedEventListener
{
    public function handle(InventoryMovementCreatedEvent $event)
    {
        $movement = $event->inventoryMovement;

        if ($movement->inventory->last_movement_at > $movement->occurred_at) {
            return;
        }

        $this->updateInventoryRecord($movement);
    }

    private function updateInventoryRecord(InventoryMovement $movement): void
    {
        $attributes = [
            'quantity' => $movement->quantity_after,
            'last_movement_id' => $movement->id,
            'first_movement_at' => Carbon::parse($movement->occurred_at)->min($movement->inventory->first_movement_at)->toDateTimeString(),
            'last_movement_at' => Carbon::parse($movement->occurred_at)->max($movement->inventory->last_movement_at)->toDateTimeString(),
        ];

        switch ($movement->type) {
            case $movement::TYPE_SALE:
                $attributes['first_sold_at'] = Carbon::parse($movement->occurred_at)->min($movement->inventory->first_sold_at)->toDateTimeString();
                $attributes['last_sold_at'] = Carbon::parse($movement->occurred_at)->max($movement->inventory->last_sold_at)->toDateTimeString();
                break;

            case $movement::TYPE_STOCKTAKE:
                $attributes['first_counted_at'] = Carbon::parse($movement->occurred_at)->min($movement->inventory->first_counted_at)->toDateTimeString();
                $attributes['last_counted_at'] = Carbon::parse($movement->occurred_at)->max($movement->inventory->last_counted_at)->toDateTimeString();
                break;

            default:
                if ($movement->quantity_delta > 0) {
                    $attributes['first_received_at'] = Carbon::parse($movement->occurred_at)->min($movement->inventory->first_received_at)->toDateTimeString();
                    $attributes['last_received_at'] = Carbon::parse($movement->occurred_at)->max($movement->inventory->last_received_at)->toDateTimeString();
                }
                break;
        }

        $movement->inventory->update($attributes);
    }
}
