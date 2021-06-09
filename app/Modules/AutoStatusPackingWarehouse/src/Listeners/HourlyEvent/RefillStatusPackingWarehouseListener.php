<?php

namespace App\Modules\AutoStatusPackingWarehouse\src\Listeners\HourlyEvent;

use App\Events\HourlyEvent;
use App\Modules\AutoStatus\src\Jobs\Refill\RefillPackingWarehouseJob;

/**
 * Class UpdateClosedAt
 * @package App\Listeners\Order
 */
class RefillStatusPackingWarehouseListener
{
    /**
     * Handle the event.
     *
     * @param HourlyEvent $event
     * @return void
     */
    public function handle(HourlyEvent $event)
    {
        RefillPackingWarehouseJob::dispatch();
    }
}
