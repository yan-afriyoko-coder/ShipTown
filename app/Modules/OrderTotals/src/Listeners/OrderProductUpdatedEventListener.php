<?php

namespace App\Modules\OrderTotals\src\Listeners;

use App\Events\OrderProduct\OrderProductUpdatedEvent;
use App\Modules\OrderTotals\src\Jobs\UpdateOrderProductTotals;

class OrderProductUpdatedEventListener
{
    public function handle(OrderProductUpdatedEvent $event)
    {
        UpdateOrderProductTotals::dispatchNow($event->orderProduct->order_id);
    }
}
