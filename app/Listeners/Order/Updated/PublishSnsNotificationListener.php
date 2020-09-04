<?php

namespace App\Listeners\Order\Updated;

use App\Events\Order\UpdatedEvent;
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
            'orders_events',
            $event->getOrder()->toJson()
        );
    }
}
