<?php

namespace App\Listeners;

use App\Events\EventTypes;
use App\Http\Controllers\SnsTopicController;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

/**
 * Class PublishSnsMessage
 * @package App\Listeners
 */
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

    /**
     * @param EventTypes $event
     */
    public function on_order_created(EventTypes $event)
    {
        $this->publishMessage($event, "orders");
    }

    /**
     * @param EventTypes $event
     */
    public function on_product_created(EventTypes $event)
    {
        $this->publishMessage($event,'products');
    }

    /**
     * @param EventTypes $event
     */
    public function on_product_updated(EventTypes $event)
    {
        $updated_event = $event;

        $updated_event->data = $event->data["new"];
        $updated_event->data["original"] = $event->data["original"];
        $updated_event->data["new"] = $event->data["new"];

        $this->publishMessage($updated_event->data,'products');
    }

    /**
     * @param EventTypes $event
     * @param $topic_prefix
     */
    private function publishMessage(EventTypes $event, $topic_prefix): void
    {
        $snsTopic = new SnsTopicController($topic_prefix);

        $snsTopic->publish_message(json_encode($event->data));
    }

}
