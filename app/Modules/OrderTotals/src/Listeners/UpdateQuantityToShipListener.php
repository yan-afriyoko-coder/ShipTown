<?php

namespace App\Modules\OrderTotals\src\Listeners;

use App\Events\OrderProduct\OrderProductCreatedEvent;
use App\Events\OrderProduct\OrderProductUpdatedEvent;

/**
 *
 */
class UpdateQuantityToShipListener
{
    /**
     * @param OrderProductUpdatedEvent | OrderProductCreatedEvent $event
     */
    public function handle($event)
    {
        //
    }
}
