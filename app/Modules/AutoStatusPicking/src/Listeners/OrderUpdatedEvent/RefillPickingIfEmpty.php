<?php

namespace App\Modules\AutoStatusPicking\src\Listeners\OrderUpdatedEvent;

use App\Events\Order\OrderUpdatedEvent;
use App\Modules\AutoStatusPicking\src\Jobs\RefillPickingIfEmptyJob;

/**
 * Class SetPackingWebStatus.
 */
class RefillPickingIfEmpty
{
    /**
     * Handle the event.
     *
     *
     * @return void
     */
    public function handle(OrderUpdatedEvent $event)
    {
        if ($event->getOrder()->isAttributeChanged('status_code')
            and ($event->getOrder()->getPreviousOrderStatus()->code === 'picking')) {
            RefillPickingIfEmptyJob::dispatch();
        }
    }
}
