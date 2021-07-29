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
        FireActiveOrderCheckEventJob::dispatch($event->order)
            ->delay(now()->addMinutes(5));
    }
}
