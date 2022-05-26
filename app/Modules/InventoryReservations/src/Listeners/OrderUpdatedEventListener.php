<?php

namespace App\Modules\InventoryReservations\src\Listeners;

use App\Events\Order\OrderUpdatedEvent;
use App\Models\OrderProduct;
use App\Modules\InventoryReservations\src\Jobs\UpdateInventoryQuantityReservedJob;

class OrderUpdatedEventListener
{
    /**
     * Handle the event.
     *
     * @param OrderUpdatedEvent $event
     *
     * @return void
     */
    public function handle(OrderUpdatedEvent $event)
    {
        if ($event->order->isAttributeNotChanged('is_active')) {
            return;
        }

        $event->order->orderProducts()
            ->select('product_id')
            ->whereNotNull('product_id')
            ->distinct()
            ->get()
            ->each(function (OrderProduct $orderProduct) {
                UpdateInventoryQuantityReservedJob::dispatchNow($orderProduct->product_id);
            });
    }
}
