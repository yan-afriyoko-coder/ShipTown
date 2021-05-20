<?php

namespace App\Listeners\Order;

use App\Events\Order\OrderCreatedEvent;
use App\Modules\AutoPilot\src\Jobs\CheckIfOrderOutOfStockJob;

class OrderCreatedEventListener
{
    /**
     * Handle the event.
     *
     * @param OrderCreatedEvent $event
     * @return void
     */
    public function handle(OrderCreatedEvent $event)
    {
        CheckIfOrderOutOfStockJob::dispatch($event->order);
    }
}
