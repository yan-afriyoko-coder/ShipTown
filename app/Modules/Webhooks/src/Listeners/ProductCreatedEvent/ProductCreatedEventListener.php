<?php

namespace App\Modules\Webhooks\src\Listeners\ProductCreatedEvent;

use App\Events\Product\ProductCreatedEvent;

class ProductCreatedEventListener
{
    /**
     * Handle the event.
     *
     * @param ProductCreatedEvent $event
     *
     * @return void
     */
    public function handle(ProductCreatedEvent $event)
    {
        activity()->withoutLogs(function () use ($event) {
            $event->getProduct()->attachTag(config('webhooks.tags.awaiting.name'));
        });
    }
}
