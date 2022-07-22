<?php

namespace App\Modules\Webhooks\src\Listeners;

use App\Events\Order\OrderCreatedEvent;

/**
 * Class AttachAwaitingPublishTagListener.
 */
class OrderCreatedEventListener
{
    /**
     * Handle the event.
     *
     * @param OrderCreatedEvent $event
     *
     * @return void
     */
    public function handle(OrderCreatedEvent $event)
    {
        activity()->withoutLogs(function () use ($event) {
            $event->getOrder()->attachTag(config('webhooks.tags.awaiting.name'));
        });
    }
}
