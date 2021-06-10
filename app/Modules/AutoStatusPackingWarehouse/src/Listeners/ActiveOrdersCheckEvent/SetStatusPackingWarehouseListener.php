<?php

namespace App\Modules\AutoStatusPackingWarehouse\src\Listeners\ActiveOrdersCheckEvent;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Events\Order\OrderUpdatedEvent;
use App\Services\OrderService;

/**
 * Class SetPackingWebStatus
 * @package App\Listeners\Order
 */
class SetStatusPackingWarehouseListener
{
    /**
     * Handle the event.
     *
     * @param ActiveOrderCheckEvent $event
     * @return void
     */
    public function handle(ActiveOrderCheckEvent $event)
    {
        $sourceLocationId = 99;
        $newOrderStatus = 'packing_warehouse';

        $order = $event->getOrder();

        if ($order->status_code !== 'paid') {
            return;
        }

        if (OrderService::canFulfill($order, $sourceLocationId)) {
            $order->log("Possible to fulfill from location $sourceLocationId, changing order status");
            $order->update(['status_code' => $newOrderStatus]);
        }
    }
}
