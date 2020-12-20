<?php

namespace App\Listeners\Order\Updated;

use App\Events\Order\OrderUpdatedEvent;
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
     * @param OrderUpdatedEvent $event
     * @return void
     */
    public function handle(OrderUpdatedEvent $event)
    {
        PublishSnsNotificationJob::dispatch(
            'orders_events',
            $event->getOrder()->toJson()
        );
    }
}
