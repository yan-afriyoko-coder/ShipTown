<?php

namespace App\Modules\Automations\src\Listeners;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Modules\Automations\src\Jobs\RunAutomationsOnActiveOrdersJob;

class ActiveOrderCheckEventListener
{
    /**
     * @param ActiveOrderCheckEvent $event
     */
    public function handle(ActiveOrderCheckEvent $event)
    {
        RunAutomationsOnActiveOrdersJob::dispatch($event->order->getKey());
    }
}
