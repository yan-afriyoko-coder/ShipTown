<?php

namespace App\Listeners\Order\Updated;

use App\Events\Order\UpdatedEvent;
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
     * @param UpdatedEvent $event
     * @return void
     */
    public function handle(UpdatedEvent $event)
    {
        if ($event->getOrder()->isNotStatusCode('processing')) {
            return;
        }

        SetStatusPaidIfPaidJob::dispatch($event->getOrder());
    }
}
