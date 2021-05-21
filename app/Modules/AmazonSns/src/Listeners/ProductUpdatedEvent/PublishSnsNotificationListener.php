<?php

namespace App\Modules\AmazonSns\src\Listeners\ProductUpdatedEvent;

use App\Events\Product\ProductUpdatedEvent;
use App\Modules\AmazonSns\src\Jobs\PublishSnsNotificationJob;

class PublishSnsNotificationListener
{
    /**
     * Handle the event.
     *
     * @param ProductUpdatedEvent $event
     * @return void
     */
    public function handle(ProductUpdatedEvent $event)
    {
        PublishSnsNotificationJob::dispatch(
            'products_events',
            $event->getProduct()->toJson()
        );
    }
}
