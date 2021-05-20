<?php

namespace App\Modules\Sns\src\Listeners\OrderCreatedEvent;

use App\Events\Order\OrderCreatedEvent;
use App\Modules\Sns\src\Jobs\PublishSnsNotificationJob;

class PublishSnsNotificationListener
{
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
