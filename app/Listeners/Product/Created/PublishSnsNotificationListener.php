<?php

namespace App\Listeners\Product\Created;

use App\Events\Product\ProductCreatedEvent;
use App\Jobs\Modules\Sns\PublishSnsNotificationJob;

class PublishSnsNotificationListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

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
