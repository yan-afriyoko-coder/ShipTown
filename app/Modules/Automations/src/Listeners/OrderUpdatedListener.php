<?php

namespace App\Modules\Automations\src\Listeners;

use App\Events\Order\OrderUpdatedEvent;
use App\Modules\Automations\src\Jobs\RunAutomationsOnActiveOrdersJob;

class OrderUpdatedListener
{
    /**
     * @param OrderUpdatedEvent $event
     */
    public function handle(OrderUpdatedEvent $event)
    {
        RunAutomationsOnActiveOrdersJob::dispatch($event->order->getKey());
    }
}
