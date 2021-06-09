<?php

namespace App\Modules\AutoStatusPackingWarehouse\src\Listeners\OrderUpdated;

use App\Events\HourlyEvent;
use App\Events\Order\OrderUpdatedEvent;
use App\Models\OrderProduct;
use App\Modules\AutoStatus\src\Jobs\Refill\RefillPackingWarehouseJob;
use App\Services\OrderService;

/**
 * Class UpdateClosedAt
 * @package App\Listeners\Order
 */
class SetStatusPackingWarehouseListener
{
    /**
     * Handle the event.
     *
     * @param OrderUpdatedEvent $event
     * @return void
     */
    public function handle(OrderUpdatedEvent $event)
    {
        $order = $event->getOrder();

        if (($order->status_code === 'paid') && (OrderService::canFulfill($order, 99))) {
                $order->update(['status_code' => 'packing_warehouse']);
        }
    }
}
