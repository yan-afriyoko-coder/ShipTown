<?php

namespace App\Modules\AutoStatusSingleLineOrders\src\Listeners\HourlyEvent;

use App\Events\HourlyEvent;
use App\Modules\AutoStatusSingleLineOrders\src\Jobs\RefillSingleLineOrdersJob;

/**
 * Class UpdateClosedAt
 * @package App\Listeners\Order
 */
class RefillStatusSingleLineOrdersListener
{
    /**
     * Handle the event.
     *
     * @param HourlyEvent $event
     * @return void
     */
    public function handle(HourlyEvent $event)
    {
        // dispatching job for monitoring purposes
        RefillSingleLineOrdersJob::dispatch();
    }
}
