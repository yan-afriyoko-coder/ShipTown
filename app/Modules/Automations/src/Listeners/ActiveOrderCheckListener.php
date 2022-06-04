<?php

namespace App\Modules\Automations\src\Listeners;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Modules\Automations\src\Jobs\RunAutomationsOnActiveOrdersJob;
use App\Modules\Automations\src\Services\AutomationService;

class ActiveOrderCheckListener
{
    /**
     * @param ActiveOrderCheckEvent $event
     */
    public function handle(ActiveOrderCheckEvent $event)
    {
        RunAutomationsOnActiveOrdersJob::dispatch($event->order->getKey());
    }
}
