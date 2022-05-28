<?php

namespace App\Modules\OrderTotals\src\Listeners;

use App\Events\OrderProduct\OrderProductUpdatedEvent;
use App\Modules\OrderTotals\src\Jobs\UpdateOrdersIsActiveJob;

class OrderProductUpdatedEventListener
{
    public function handle(OrderProductUpdatedEvent $event)
    {
        UpdateOrdersIsActiveJob::dispatchNow($event->orderProduct->order_id);
    }
}
