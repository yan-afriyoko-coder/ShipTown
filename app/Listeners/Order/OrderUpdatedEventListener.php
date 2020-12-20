<?php

namespace App\Listeners\Order;

use App\Events\Order\OrderUpdatedEvent;
use App\Jobs\Modules\Sns\PublishSnsNotificationJob;
use App\Jobs\Orders\SetStatusPaidIfPaidJob;
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
        $this->moveOrderFromPickingToPackingWeb($event);
        $this->changeStatusToReadyIfPacked($event);
        $this->updateOrderClosedAt($event);
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
        if ($event->getOrder()->isNotStatusCode('processing')) {
            return;
        }

        SetStatusPaidIfPaidJob::dispatch($event->getOrder());
    }

    public function moveOrderFromPickingToPackingWeb(OrderUpdatedEvent $event)
    {
        if ($event->getOrder()->isNotStatusCode('picking')) {
            return;
        }

        if ($event->getOrder()->is_picked) {
            $event->getOrder()->update(['status_code' => 'packing_web']);
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
        if ($event->getOrder()->status_code === 'ready') {
            return;
        }

        if ($event->getOrder()->isStatusCodeNotIn(OrderStatus::getActiveStatusCodesList())) {
            return;
        }

        if ($event->getOrder()->is_packed) {
            $event->getOrder()->update(['status_code' => 'ready']);
        }
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
