<?php

namespace App\Modules\DpdUk\src\Listeners\OrderShipmentCreatedEvent;

use App\Events\OrderShipment\OrderShipmentCreatedEvent;
use App\Modules\DpdUk\src\Jobs\GenerateLabelDocumentJob;

class DispatchNowGenerateLabelJobListener
{
    public function handle(OrderShipmentCreatedEvent $event)
    {
        GenerateLabelDocumentJob::dispatchNow($event);
    }
}
