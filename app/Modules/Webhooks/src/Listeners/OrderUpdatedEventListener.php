<?php

namespace App\Modules\Webhooks\src\Listeners;

use App\Events\Order\OrderUpdatedEvent;

/**
 * Class AttachAwaitingPublishTagListener.
 */
class OrderUpdatedEventListener
{
    /**
     * Handle the event.
     *
     * @param OrderUpdatedEvent $event
     *
     * @return void
     */
    public function handle(OrderUpdatedEvent $event)
    {
        activity()->withoutLogs(function () use ($event) {
            $event->getOrder()->attachTag(config('webhooks.tags.awaiting.name'));
        });
    }
}
