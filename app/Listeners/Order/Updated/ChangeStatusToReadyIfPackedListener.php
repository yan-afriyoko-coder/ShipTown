<?php

namespace App\Listeners\Order\Updated;

use App\Events\Order\UpdatedEvent;
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
     * @param UpdatedEvent $event
     * @return void
     */
    public function handle(UpdatedEvent $event)
    {
        if ($event->getOrder()->isStatusCodeNotIn(OrderStatus::getActiveStatusCodesList())) {
            return;
        }

        if ($event->getOrder()->is_packed) {
            $event->getOrder()->update(['status_code' => 'ready']);
        }
    }
}
