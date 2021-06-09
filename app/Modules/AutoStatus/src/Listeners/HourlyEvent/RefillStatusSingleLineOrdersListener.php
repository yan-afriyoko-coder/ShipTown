<?php

namespace App\Modules\AutoStatus\src\Listeners\HourlyEvent;

use App\Events\HourlyEvent;
use App\Modules\AutoStatus\src\Jobs\Refill\RefillSingleLineOrdersJob;

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
        RefillSingleLineOrdersJob::dispatch();
    }
}
