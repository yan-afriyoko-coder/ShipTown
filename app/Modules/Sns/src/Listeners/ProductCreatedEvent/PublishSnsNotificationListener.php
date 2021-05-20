<?php

namespace App\Modules\Sns\src\Listeners\ProductCreatedEvent;

use App\Events\Product\ProductCreatedEvent;
use App\Modules\Sns\src\Jobs\PublishSnsNotificationJob;

class PublishSnsNotificationListener
{
    /**
     * Handle the event.
     *
     * @param ProductCreatedEvent $event
     * @return void
     */
    public function handle(ProductCreatedEvent $event)
    {
        PublishSnsNotificationJob::dispatch(
            'products_events',
            $event->getProduct()->toJson()
        );
    }
}
