<?php

namespace App\Observers;

use App\Events\OrderProductShipmentCreatedEvent;
use App\Models\OrderProductShipment;

class OrderProductShipmentObserver
{
    /**
     * Handle the order product "created" event.
     *
     * @param OrderProductShipment $orderProductShipment
     *
     * @return void
     */
    public function created(OrderProductShipment $orderProductShipment)
    {
        activity()->on($orderProductShipment->order)
            ->causedBy($orderProductShipment->user)
            ->withProperties([
                'sku' => $orderProductShipment->sku_shipped,
                'quantity' => $orderProductShipment->quantity_shipped,
                'warehouse_code' => $orderProductShipment->warehouse->code
            ]);

        activity()->on($orderProductShipment->product)
            ->causedBy($orderProductShipment->user)
            ->withProperties([
                'order_number' => $orderProductShipment->order->order_number,
                'quantity' => $orderProductShipment->quantity_shipped,
                'warehouse_code' => $orderProductShipment->warehouse->code
            ]);

        OrderProductShipmentCreatedEvent::dispatch($orderProductShipment);
    }
}
