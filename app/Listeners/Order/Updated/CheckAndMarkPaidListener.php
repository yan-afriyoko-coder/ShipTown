<?php

namespace App\Listeners\Order\Updated;

use App\Events\Order\OrderUpdatedEvent;
use App\Jobs\Orders\SetStatusPaidIfPaidJob;

class CheckAndMarkPaidListener
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
        if ($event->getOrder()->isNotStatusCode('processing')) {
            return;
        }

        SetStatusPaidIfPaidJob::dispatch($event->getOrder());
    }
}
