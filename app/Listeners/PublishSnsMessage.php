<?php

namespace App\Listeners;

use App\Events\EventTypes;
use App\Http\Controllers\SnsTopicController;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class PublishSnsMessage
{
    /**
     * Register the listeners for the subscriber.
     *
     * @param Dispatcher $events
     */
    public function subscribe($events)
    {
        $events->listen(EventTypes::ORDER_CREATED,'App\Listeners\PublishSnsMessage@on_order_created');

        //products
        $events->listen(EventTypes::PRODUCT_CREATED,'App\Listeners\PublishSnsMessage@on_product_created');
        $events->listen(EventTypes::PRODUCT_UPDATED,'App\Listeners\PublishSnsMessage@on_product_updated');
    }


    public function on_order_created(EventTypes $event) {

        $order = $event->data;

        $snsTopic = new SnsTopicController('orders');

        $snsTopic->publish_message(json_encode($order));
    }

    public function on_product_created(EventTypes $event)
    {
        $product = $event->data;

        $snsTopic = new SnsTopicController('products');

        $snsTopic->publish_message(json_encode($product));
    }

    public function on_product_updated(EventTypes $event)
    {
        $product = $event->data;

        $snsTopic = new SnsTopicController('products');

        $snsTopic->publish_message(json_encode($product));
    }

}
