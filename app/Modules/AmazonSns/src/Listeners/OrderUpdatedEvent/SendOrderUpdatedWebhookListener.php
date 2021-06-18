<?php

namespace App\Modules\AmazonSns\src\Listeners\OrderUpdatedEvent;

use App\Events\Order\OrderUpdatedEvent;
use App\Modules\AmazonSns\src\Jobs\PublishSnsNotificationJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use romanzipp\QueueMonitor\Traits\IsMonitored;

/**
 * Class SendOrderUpdatedWebhookListener
 * @package App\Modules\AmazonSns\src\Listeners\OrderUpdatedEvent
 */
class SendOrderUpdatedWebhookListener implements ShouldQueue
{
    use IsMonitored;

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

        $this->queueData([
            'order_id' => $upToDateOrder->getKey(),
            'order_number' => $upToDateOrder->order_number
        ]);

        PublishSnsNotificationJob::dispatchNow('orders_events', $upToDateOrder->toJson());
    }
}
