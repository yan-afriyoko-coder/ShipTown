<?php

namespace App\Modules\InventoryReservations\src\Listeners;

use App\Events\Order\OrderUpdatedEvent;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\OrderStatus;

class OrderUpdatedListener
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
     * @param OrderUpdatedEvent $event
     * @return void
     */
    public function handle(OrderUpdatedEvent $event)
    {
//        $this->reserveOrReleaseStock($event->getOrder());
    }

    /**
     * @param Order $order
     */
    private function reserveOrReleaseStock(Order $order)
    {
        $previous_status = OrderStatus::firstOrCreate([
            'code' => $order->getOriginal('order_status')
        ], [
            'name' => $order->getOriginal('order_status'),
        ]);

        if ($previous_status->reserves_stock === $order->orderStatus->reserves_stock) {
            return;
        }

        if ($order->orderStatus->reserves_stock) {
            $this->reserveOrderProducts($order);
        } else {
            $this->releaseOrderProducts($order);
        }
    }

    /**
     * @param Order $order
     */
    private function reserveOrderProducts(Order $order): void
    {
        $order->orderProducts->each(function (OrderProduct $orderProduct) {
            if ($orderProduct->product) {
                $inventory = $orderProduct->product->inventory()->where(['location_id' => 999])->first();
                $inventory->quantity_reserved += $orderProduct->quantity_to_ship;
                $orderProduct->save();
            }
        });
    }

    /**
     * @param Order $order
     */
    private function releaseOrderProducts(Order $order): void
    {
        $order->orderProducts->each(function (OrderProduct $orderProduct) {
            if ($orderProduct->product) {
                $inventory = $orderProduct->product->inventory()->where(['location_id' => 999])->first();
                $inventory->quantity_reserved -= $orderProduct->quantity_to_ship;
                $orderProduct->save();
            }
        });
    }
}
