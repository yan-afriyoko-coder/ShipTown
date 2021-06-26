<?php

namespace App\Modules\InventoryReservations\src\Listeners;

use App\Events\OrderProduct\OrderProductCreatedEvent;
use App\Models\Inventory;

class OrderProductCreatedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param OrderProductCreatedEvent $event
     *
     * @return void
     */
    public function handle(OrderProductCreatedEvent $event)
    {
        $orderProduct = $event->orderProduct;

        if ($orderProduct->product_id === null) {
            return;
        }

        if ($orderProduct->order->orderStatus->reserves_stock === false) {
            return;
        }

        $inventory = Inventory::firstOrNew([
            'product_id'  => $orderProduct->product_id,
            'location_id' => 999,
        ]);

        $inventory->quantity_reserved += $orderProduct->quantity_to_ship;
        $inventory->save();
    }
}
