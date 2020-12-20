<?php

namespace App\Listeners\Order\Updated;

use App\Events\Order\OrderUpdatedEvent;

class ChangeToPackingWebStatusListener
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
        if ($event->getOrder()->isNotStatusCode('picking')) {
            return;
        }

        if ($event->getOrder()->is_picked) {
            $event->getOrder()->update(['status_code' => 'packing_web']);
        }
    }
}
