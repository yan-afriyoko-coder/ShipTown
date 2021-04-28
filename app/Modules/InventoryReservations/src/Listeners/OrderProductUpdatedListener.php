<?php

namespace App\Modules\InventoryReservations\src\Listeners;

use App\Events\OrderProduct\OrderProductUpdatedEvent;
use App\Models\Inventory;

class OrderProductUpdatedListener
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
     * @param OrderProductUpdatedEvent $event
     * @return void
     */
    public function handle(OrderProductUpdatedEvent  $event)
    {
        $orderProduct = $event->orderProduct;

        if ($orderProduct->product_id === null) {
            return;
        }

        $delta = $orderProduct->getAttribute('quantity_to_ship') - $orderProduct->getOriginal('quantity_to_ship') ?? 0;

        if ($delta === 0) {
            return;
        }

        if ($orderProduct->order->orderStatus->reserves_stock === false) {
            return;
        }

        $inventory = Inventory::firstOrNew([
            'product_id' =>  $orderProduct->product_id,
            'location_id' => 999,
        ]);
        $inventory->quantity_reserved += $delta;
        $inventory->save();
    }
}
