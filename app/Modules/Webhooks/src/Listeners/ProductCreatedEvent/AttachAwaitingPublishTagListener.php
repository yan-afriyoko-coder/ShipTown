<?php

namespace App\Modules\Webhooks\src\Listeners\ProductCreatedEvent;

use App\Events\Product\ProductCreatedEvent;

class AttachAwaitingPublishTagListener
{
    /**
     * Handle the event.
     *
     * @param ProductCreatedEvent $event
     * @return void
     */
    public function handle(ProductCreatedEvent $event)
    {
        $event->getProduct()->attachTag(config('webhooks.tags.awaiting.name'));
    }
}
