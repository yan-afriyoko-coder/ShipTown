<?php

namespace App\Modules\Automations\src\Listeners;

use App\Events\ShippingLabelCreatedEvent;
use App\Modules\Automations\src\Jobs\RunEnabledAutomationsOnSpecificOrderJob;

class ShippingLabelCreatedListener
{
    /**
     * @param ShippingLabelCreatedEvent $event
     */
    public function handle(ShippingLabelCreatedEvent $event)
    {
        RunEnabledAutomationsOnSpecificOrderJob::dispatchNow($event->shippingLabel->order_id);
    }
}
