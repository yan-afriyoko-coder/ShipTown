<?php

namespace App\Listeners;

use App\Events\EventTypes;
use App\Http\Controllers\SnsTopicController;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SnsSubscriber
{
    /**
     * Register the listeners for the subscriber.
     *
     * @param Dispatcher $events
     */
    public function subscribe($events)
    {
        $events->listen(
            EventTypes::ORDER_CREATED,
            'App\Listeners\SnsSubscriber@on_order_created'
        );
    }


    public function on_order_created(EventTypes $event) {

        $snsTopic = new SnsTopicController('orders');

        $order = $event->data;

        $snsTopic->publish_message(json_encode($order));
    }

}
