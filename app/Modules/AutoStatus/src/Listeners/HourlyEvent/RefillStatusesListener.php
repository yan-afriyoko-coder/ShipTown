<?php

namespace App\Modules\AutoStatus\src\Listeners\HourlyEvent;

use App\Events\HourlyEvent;
use App\Modules\AutoStatus\src\Jobs\RefillStatusesJob;

/**
 * Class UpdateClosedAt
 * @package App\Listeners\Order
 */
class RefillStatusesListener
{
    /**
     * Handle the event.
     *
     * @param HourlyEvent $event
     * @return void
     */
    public function handle(HourlyEvent $event)
    {
        RefillStatusesJob::dispatch();
    }
}
