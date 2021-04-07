<?php

namespace App\Listeners\Order;

use App\Events\Order\OrderUpdatedEvent;
use App\Modules\AutoPilot\src\Jobs\CheckIfOrderOutOfStockJob;
use App\Modules\Sns\src\Jobs\PublishSnsNotificationJob;
use App\Modules\AutoPilot\src\Jobs\SetStatusPaidIfPaidJob;
use App\Models\OrderStatus;

class OrderUpdatedEventListener
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
     * @param OrderUpdatedEvent $event
     * @return void
     */
    public function handle(OrderUpdatedEvent $event)
    {
        $this->markedIfPayed($event);
        $this->changeStatusToReadyIfPacked($event);
        $this->updateOrderClosedAt($event);
        CheckIfOrderOutOfStockJob::dispatch($event->getOrder());
        $this->publishSnsNotification($event);
    }

    /**
     * Handle the event.
     *
     * @param OrderUpdatedEvent $event
     * @return void
     */
    public function publishSnsNotification(OrderUpdatedEvent $event)
    {
        PublishSnsNotificationJob::dispatch(
            'orders_events',
            $event->getOrder()->toJson()
        );
    }

    /**
     * Handle the event.
     *
     * @param OrderUpdatedEvent $event
     * @return void
     */
    public function markedIfPayed(OrderUpdatedEvent $event)
    {
        if ($event->getOrder()->isStatusCode('processing')) {
            SetStatusPaidIfPaidJob::dispatch($event->getOrder());
        }
    }

    /**
     * Handle the event.
     *
     * @param OrderUpdatedEvent $event
     * @return void
     */
    public function changeStatusToReadyIfPacked(OrderUpdatedEvent $event)
    {
        if ($event->getOrder()->isStatusCodeIn(OrderStatus::getClosedStatuses())) {
            return;
        }

        if ($event->getOrder()->status_code === 'ready') {
            return;
        }

        if ($event->getOrder()->is_packed === false) {
            return;
        }

        $event->getOrder()->update(['status_code' => 'ready']);
    }

    /**
     * Handle the event.
     *
     * @param OrderUpdatedEvent $event
     * @return void
     */
    public function updateOrderClosedAt(OrderUpdatedEvent $event)
    {
        if ($event->getOrder()['status_code'] !== $event->getOrder()->getOriginal('status_code')) {
            if (($event->getOrder()->order_closed_at === null) && (OrderStatus::isComplete($event->getOrder()['status_code']))) {
                $event->getOrder()->update(['order_closed_at' => now()]);
            }
        }
    }
}
