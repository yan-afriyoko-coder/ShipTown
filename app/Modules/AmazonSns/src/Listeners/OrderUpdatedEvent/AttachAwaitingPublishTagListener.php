<?php

namespace App\Modules\AmazonSns\src\Listeners\OrderUpdatedEvent;

use App\Events\Order\OrderUpdatedEvent;

/**
 * Class AttachAwaitingPublishTagListener
 * @package App\Modules\AmazonSns\src\Listeners\OrderUpdatedEvent
 */
class AttachAwaitingPublishTagListener
{
    /**
     * Handle the event
     *
     * @param OrderUpdatedEvent $event
     * @return void
     */
    public function handle(OrderUpdatedEvent $event)
    {
        $event->getOrder()->attachTag(config('webhooks.tags.awaiting.name'));
    }
}
