<?php

namespace App\Modules\InventoryReservations\src\Listeners\OrderProductUpdatedEvent;

use App\Events\OrderProduct\OrderProductUpdatedEvent;
use App\Models\Inventory;
use App\Models\OrderProduct;

class UpdateQuantityReservedListener
{
    /**
     * Handle the event.
     *
     * @param OrderProductUpdatedEvent $event
     * @return void
     */
    public function handle(OrderProductUpdatedEvent $event)
    {
        if ($this->shouldModifyReservation($event->orderProduct)) {
            $inventory = Inventory::firstOrNew([
                'product_id' =>  $event->orderProduct->product_id,
                'location_id' => 999,
            ]);
            $inventory->quantity_reserved -= $event->orderProduct->getOriginal('quantity_to_ship') ?? 0;
            $inventory->quantity_reserved += $event->orderProduct->getAttribute('quantity_to_ship') ?? 0;
            $inventory->save();
        }
    }

    /**
     * @param OrderProduct $orderProduct
     * @return bool
     */
    public function shouldModifyReservation(OrderProduct $orderProduct): bool
    {
        if ($orderProduct->product_id === null) {
            return false;
        }

        if ($orderProduct->order->orderStatus->reserves_stock === false) {
            return false;
        }

        return $orderProduct->isAnyAttributeChanged(['quantity_to_ship']);
    }
}
