<?php

namespace App\Listeners\Product\Created;

use App\Events\Product\UpdatedEvent;
use App\Http\Controllers\SnsController;

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
        $snsTopic = new SnsController('products_events');

        $snsTopic->publish(json_encode($event->getProduct()->toArray()));
    }
}
