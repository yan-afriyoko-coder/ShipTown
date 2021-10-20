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
        OrderProductShipmentCreatedEvent::dispatch($orderProductShipment);
    }
}
