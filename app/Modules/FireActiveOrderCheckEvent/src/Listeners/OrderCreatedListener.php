<?php

namespace App\Modules\FireActiveOrderCheckEvent\src\Listeners;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Events\Order\OrderCreatedEvent;
use App\Modules\FireActiveOrderCheckEvent\src\Jobs\FireActiveOrderCheckEventJob;

/**
 * Class SetPackingWebStatus.
 */
class OrderCreatedListener
{
    /**
     * Handle the event.
     *
     * @param OrderCreatedEvent $event
     *
     * @return void
     */
    public function handle(OrderCreatedEvent $event)
    {
        if ($event->order->is_editing) {
            return;
        }

        FireActiveOrderCheckEventJob::dispatch($event->order)
            // we let things settle down
            // set 5min cool down period
            ->delay(
                now()->addMinutes(5)
            );
    }
}
