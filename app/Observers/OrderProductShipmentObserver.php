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
        //  log activity on order
        if ($orderProductShipment->order) {
            activity()->on($orderProductShipment->order)
                ->causedBy($orderProductShipment->user)
                ->withProperties([
                    'sku' => $orderProductShipment->sku_shipped,
                    'quantity' => $orderProductShipment->quantity_shipped,
                    'warehouse_code' => $orderProductShipment->warehouse->code
                ])
                ->log('shipped');
        }

        // place log on product
        if ($orderProductShipment->product) {
            activity()->on($orderProductShipment->product)
                ->causedBy($orderProductShipment->user)
                ->withProperties([
                    'sku' => $orderProductShipment->sku_shipped,
                    'order_number' => $orderProductShipment->order->order_number,
                    'quantity' => $orderProductShipment->quantity_shipped,
                    'warehouse_code' => data_get($orderProductShipment, 'warehouse.code')
                ])
                ->log('shipped');
        }

        OrderProductShipmentCreatedEvent::dispatch($orderProductShipment);
    }
}
