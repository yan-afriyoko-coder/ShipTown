<?php

namespace App\Modules\AutoStatus\src\Listeners\HourlyEvent;

use App\Events\HourlyEvent;
use App\Modules\AutoStatus\src\Jobs\RefillStatusPicking;

/**
 * Class UpdateClosedAt
 * @package App\Listeners\Order
 */
class RefillStatusPickingListener
{
    /**
     * Handle the event.
     *
     * @param HourlyEvent $event
     * @return void
     */
    public function handle(HourlyEvent $event)
    {
        RefillStatusPicking::dispatch();
    }
}
