<?php

namespace App\Modules\AmazonSns\src\Listeners\OrderCreatedEvent;

use App\Events\Order\OrderCreatedEvent;

/**
 * Class AttachAwaitingPublishTagListener
 * @package App\Modules\AmazonSns\src\Listeners\OrderUpdatedEvent
 */
class AttachAwaitingPublishTagListener
{
    /**
     * Handle the event
     *
     * @param OrderCreatedEvent $event
     * @return void
     */
    public function handle(OrderCreatedEvent $event)
    {
        $event->getOrder()->attachTag(config('webhooks.tags.awaiting.name'));
    }
}
