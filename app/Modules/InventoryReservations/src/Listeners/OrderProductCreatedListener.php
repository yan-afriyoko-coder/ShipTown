<?php

namespace App\Modules\InventoryReservations\src\Listeners;

use App\Events\OrderProduct\OrderProductCreatedEvent;
use App\Modules\InventoryReservations\src\Jobs\UpdateQuantityReservedJob;

class OrderProductCreatedListener
{
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

        UpdateQuantityReservedJob::dispatch($orderProduct->product_id);
    }
}
