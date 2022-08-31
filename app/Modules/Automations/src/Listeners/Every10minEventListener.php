<?php

namespace App\Modules\Automations\src\Listeners;

use App\Events\Every10minEvent;
use App\Modules\Automations\src\Jobs\RunEnabledAutomationsJob;

class Every10minEventListener
{
    /**
     * @param Every10minEvent $every10minEvent
     */
    public function handle(Every10minEvent $every10minEvent)
    {
        RunEnabledAutomationsJob::dispatch();
    }
}
