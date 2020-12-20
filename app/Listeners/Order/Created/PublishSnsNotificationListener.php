<?php

namespace App\Listeners\Order\Created;

use App\Events\Order\OrderCreatedEvent;
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
     * @param OrderCreatedEvent $event
     * @return void
     */
    public function handle(OrderCreatedEvent $event)
    {
        PublishSnsNotificationJob::dispatch(
            'orders_events',
            $event->getOrder()->toJson()
        );
    }
}
