<?php

namespace App\Modules\Webhooks\src\Listeners\ProductUpdatedEvent;

use App\Events\Product\ProductUpdatedEvent;

class ProductUpdatedEventListener
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
        activity()->withoutLogs(function () use ($event) {
            $event->getProduct()->attachTag(config('webhooks.tags.awaiting.name'));
        });
    }
}
