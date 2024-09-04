<?php

namespace App\Modules\Automations\src\Listeners;

use App\Events\OrderShipment\OrderShipmentCreatedEvent;
use App\Modules\Automations\src\Jobs\RunEnabledAutomationsOnSpecificOrderJob;

class OrderShipmentCreatedEventListener
{
    public function handle(OrderShipmentCreatedEvent $event)
    {
        RunEnabledAutomationsOnSpecificOrderJob::dispatchSync($event->orderShipment->order_id);
    }
}
