<?php

namespace App\Modules\InventoryReservations\src\Listeners;

use App\Events\Order\OrderUpdatedEvent;
use App\Models\OrderProduct;
use App\Models\OrderStatus;
use App\Modules\InventoryReservations\src\Jobs\UpdateQuantityReservedJob;

class OrderUpdatedListener
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
        $previous_status = OrderStatus::firstOrCreate([
            'code' => $event->order->getOriginal('status_code'),
        ], [
            'name' => $event->order->getOriginal('status_code'),
        ]);

        if ($previous_status->reserves_stock === $event->order->orderStatus->reserves_stock) {
            return;
        }

        $event->order->orderProducts->each(function (OrderProduct $orderProduct) {
            if ($orderProduct->product) {
                UpdateQuantityReservedJob::dispatch($orderProduct->product_id);
            }
        });
    }
}
