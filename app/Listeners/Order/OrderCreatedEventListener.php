<?php

namespace App\Listeners\Order;

use App\Events\Order\OrderCreatedEvent;
use App\Jobs\Modules\Sns\PublishSnsNotificationJob;

class OrderCreatedEventListener
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
        $this->publishSnsNotification($event);
    }

    /**
     * Handle the event.
     *
     * @param OrderCreatedEvent $event
     * @return void
     */
    public function publishSnsNotification(OrderCreatedEvent $event)
    {
        PublishSnsNotificationJob::dispatch(
            'orders_events',
            $event->getOrder()->toJson()
        );
    }
}
