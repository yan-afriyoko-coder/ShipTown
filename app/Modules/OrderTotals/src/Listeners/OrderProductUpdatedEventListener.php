<?php

namespace App\Modules\OrderTotals\src\Listeners;

use App\Events\OrderProduct\OrderProductUpdatedEvent;
use App\Modules\OrderTotals\src\Jobs\UpdateOrderTotalsJob;

class OrderProductUpdatedEventListener
{
    public function handle(OrderProductUpdatedEvent $event)
    {
        UpdateOrderTotalsJob::dispatchSync($event->orderProduct->order_id);
    }
}
