<?php

namespace App\Modules\OrderTotals\src\Listeners;

use App\Events\OrderProduct\OrderProductCreatedEvent;
use App\Modules\OrderTotals\src\Jobs\UpdateOrdersIsActiveJob;

class OrderProductCreatedEventListener
{
    public function handle(OrderProductCreatedEvent $event)
    {
        UpdateOrdersIsActiveJob::dispatchNow($event->orderProduct->order_id);
    }
}
