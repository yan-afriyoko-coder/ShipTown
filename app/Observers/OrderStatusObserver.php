<?php


namespace App\Observers;

use App\Events\OrderStatus\OrderStatusUpdatedEvent;
use App\Jobs\UpdateOrdersIsActiveJob;
use App\Models\OrderStatus;

class OrderStatusObserver
{
    public function updated(OrderStatus $orderStatus)
    {
        OrderStatusUpdatedEvent::dispatch($orderStatus);

        if ($orderStatus->isAttributeChanged('order_active')) {
            UpdateOrdersIsActiveJob::dispatch($orderStatus);
        }
    }
}
