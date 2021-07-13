<?php

namespace App\Observers;

use App\Events\OrderShipment\OrderShipmentCreatedEvent;
use App\Models\OrderShipment;

class OrderShipmentObserver
{
    /**
     * Handle the order shipment "created" event.
     *
     * @param  OrderShipment  $orderShipment
     * @return void
     */
    public function created(OrderShipment $orderShipment)
    {
        OrderShipmentCreatedEvent::dispatch($orderShipment);
    }
}
