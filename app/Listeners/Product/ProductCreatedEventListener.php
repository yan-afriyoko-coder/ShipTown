<?php

namespace App\Listeners\Product;

use App\Events\Product\ProductCreatedEvent;
use App\Modules\Sns\src\Jobs\PublishSnsNotificationJob;

class ProductCreatedEventListener
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
        $this->publishSnsNotification($event);
    }

    /**
     * Handle the event.
     *
     * @param ProductCreatedEvent $event
     * @return void
     */
    public function publishSnsNotification(ProductCreatedEvent $event)
    {
        PublishSnsNotificationJob::dispatch(
            'products_events',
            $event->getProduct()->toJson()
        );
    }
}
