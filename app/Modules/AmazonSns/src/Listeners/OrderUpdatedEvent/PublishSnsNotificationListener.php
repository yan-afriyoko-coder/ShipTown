<?php

namespace App\Modules\AmazonSns\src\Listeners\OrderUpdatedEvent;

use App\Events\Order\OrderUpdatedEvent;
use App\Modules\AmazonSns\src\Jobs\PublishSnsNotificationJob;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Class PublishSnsNotificationListener
 * @package App\Modules\AmazonSns\src\Listeners\OrderUpdatedEvent
 */
class PublishSnsNotificationListener implements ShouldQueue
{
    /**
     * The time (seconds) before the job should be processed.
     *
     * We will queue it and wait few seconds
     * to make sure all listeners finished updating order
     *
     * @var int
     */
    public int $delay = 5;

    /**
     * Handle the event
     *
     * @param OrderUpdatedEvent $event
     * @return void
     */
    public function handle(OrderUpdatedEvent $event)
    {
        $upToDateOrder = $event->getOrder()->refresh();

        PublishSnsNotificationJob::dispatchNow('orders_events', $upToDateOrder->toJson());
    }
}
