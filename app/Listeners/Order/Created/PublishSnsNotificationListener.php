<?php

namespace App\Listeners\Order\Created;

use App\Events\Order\CreatedEvent;
use App\Http\Controllers\SnsController;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

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
     * @param CreatedEvent $event
     * @return void
     */
    public function handle(CreatedEvent $event)
    {
        $snsTopic = new SnsController('orders_events');

        $snsTopic->publish(json_encode($event->getOrder()->toArray()));
    }
}
