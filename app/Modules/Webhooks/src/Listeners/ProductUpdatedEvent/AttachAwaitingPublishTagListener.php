<?php

namespace App\Modules\Webhooks\src\Listeners\ProductUpdatedEvent;

use App\Events\Product\ProductUpdatedEvent;

class AttachAwaitingPublishTagListener
{
    /**
     * Handle the event.
     *
     * @param ProductUpdatedEvent $event
     *
     * @return void
     */
    public function handle(ProductUpdatedEvent $event)
    {
        $event->getProduct()->attachTag(config('webhooks.tags.awaiting.name'));
    }
}
