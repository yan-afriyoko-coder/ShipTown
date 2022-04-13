<?php

namespace App\Modules\OrderStatus\src\Listeners;

use App\Events\OrderStatus\OrderStatusUpdatedEvent;
use App\Modules\OrderStatus\src\Jobs\UpdateOrdersIsOnHoldJob;

class OrderStatusUpdatedEventListener
{
    public function handle(OrderStatusUpdatedEvent $event)
    {
        if ($event->orderStatus->isAttributeChanged('order_active')) {
            UpdateOrdersIsOnHoldJob::dispatch($event->orderStatus);
        }

        if ($event->orderStatus->isAttributeChanged('order_on_hold')) {
            UpdateOrdersIsOnHoldJob::dispatch($event->orderStatus);
        }
    }
}
