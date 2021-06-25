<?php

namespace App\Modules\AutoStatusPackingWarehouse\src\Listeners\ActiveOrdersCheckEvent;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Modules\AutoStatusPackingWarehouse\src\Jobs\SetStatusPackingWarehouseJob;
use App\Services\OrderService;

/**
 * Class SetPackingWebStatus.
 */
class SetStatusPackingWarehouseListener
{
    /**
     * Handle the event.
     *
     * @param ActiveOrderCheckEvent $event
     *
     * @return void
     */
    public function handle(ActiveOrderCheckEvent $event)
    {
        SetStatusPackingWarehouseJob::dispatchNow($event->getOrder());
    }
}
