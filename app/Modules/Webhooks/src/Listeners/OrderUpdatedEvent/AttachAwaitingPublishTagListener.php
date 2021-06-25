<?php

namespace App\Modules\Webhooks\src\Listeners\OrderUpdatedEvent;

use App\Events\Order\OrderUpdatedEvent;

/**
 * Class AttachAwaitingPublishTagListener.
 */
class AttachAwaitingPublishTagListener
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
        $event->getOrder()->attachTag(config('webhooks.tags.awaiting.name'));
    }
}
