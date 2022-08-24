<?php

namespace App\Modules\OrderTotals\src\Listeners;

use App\Events\Order\OrderCreatedEvent;
use App\Models\OrderProductTotal;

class OrderCreatedEventListener
{
    public function handle(OrderCreatedEvent $event)
    {
        OrderProductTotal::query()->firstOrCreate(['order_id' => $event->order->id], []);
    }
}
