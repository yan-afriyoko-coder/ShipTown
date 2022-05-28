<?php

namespace App\Modules\OrderTotals\src\Listeners;

use App\Events\OrderProduct\OrderProductCreatedEvent;
use App\Modules\OrderTotals\src\Jobs\UpdateOrderProductTotals;

class OrderProductCreatedEventListener
{
    public function handle(OrderProductCreatedEvent $event)
    {
        UpdateOrderProductTotals::dispatchNow($event->orderProduct->order_id);
    }
}
