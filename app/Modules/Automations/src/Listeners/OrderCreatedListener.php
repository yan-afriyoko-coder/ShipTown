<?php

namespace App\Modules\Automations\src\Listeners;

use App\Events\Order\OrderCreatedEvent;
use App\Modules\Automations\src\Jobs\RunAutomationsOnActiveOrdersJob;
use App\Modules\Automations\src\Services\AutomationService;

class OrderCreatedListener
{
    /**
     * @param OrderCreatedEvent $event
     */
    public function handle(OrderCreatedEvent $event)
    {
        RunAutomationsOnActiveOrdersJob::dispatch($event->order->getKey())
            // we let things settle down
            // set 5min cool down period
            ->delay(
                now()->addMinutes(5)
            );
    }
}
