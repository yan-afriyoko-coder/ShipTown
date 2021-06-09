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
        $order = $event->getOrder();

        if (($order->status_code === 'paid') && (OrderService::canFulfill($order, 99))) {
                $order->update(['status_code' => 'packing_warehouse']);
        }
    }
}
