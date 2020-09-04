<?php

namespace App\Listeners\Product\Created;

use App\Events\Product\UpdatedEvent;
use App\Jobs\Sns\PublishSnsNotificationJob;

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
     * @param UpdatedEvent $event
     * @return void
     */
    public function handle(UpdatedEvent $event)
    {
        PublishSnsNotificationJob::dispatch(
            'products_events',
            $event->getProduct()->toJson()
        );
    }
}
