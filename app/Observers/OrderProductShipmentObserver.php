<?php

namespace App\Observers;

use App\Models\OrderProductShipment;
use App\Events\OrderProductShipmentCreatedEvent;

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
            ->log('shipped {"sku": "' .
                $orderProductShipment->sku_shipped. '", "quantity": '.$orderProductShipment->quantity_shipped.'}');

        OrderProductShipmentCreatedEvent::dispatch($orderProductShipment);
    }
}
