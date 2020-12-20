<?php

namespace App\Listeners\Order\Updated;

use App\Events\Order\OrderUpdatedEvent;
use App\Models\OrderStatus;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class ChangeStatusToReadyIfPackedListener
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
}
