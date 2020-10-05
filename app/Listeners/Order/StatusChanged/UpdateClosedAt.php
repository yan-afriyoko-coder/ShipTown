<?php

namespace App\Listeners\Order\StatusChanged;

use App\Events\Order\StatusChangedEvent;
use App\Models\OrderStatus;

class UpdateClosedAt
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
     * @param  object  $event
     * @return void
     */
    public function handle(StatusChangedEvent $event)
    {
        if (($event->getOrder()->order_closed_at === null) && (OrderStatus::isComplete($event->getNewStatusCode()))) {
            $event->getOrder()->update(['order_closed_at' => now()]);
        }
    }
}
