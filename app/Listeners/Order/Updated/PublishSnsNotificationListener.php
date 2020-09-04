<?php

namespace App\Listeners\Order\Updated;

use App\Events\Order\UpdatedEvent;
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
     * @param UpdatedEvent $event
     * @return void
     */
    public function handle(UpdatedEvent $event)
    {
        $snsTopic = new SnsController('orders_events');

        $snsTopic->publish(json_encode($event->getOrder()->toArray()));
    }
}
