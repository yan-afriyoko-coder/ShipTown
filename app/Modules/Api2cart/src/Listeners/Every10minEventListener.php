<?php

namespace App\Modules\Api2cart\src\Listeners;

use App\Events\Every10minEvent;
use App\Modules\Api2cart\src\Jobs\DispatchImportOrdersJobs;

class Every10minEventListener
{
    /**
     * Handle the event.
     *
     * @param Every10minEvent $event
     *
     * @return void
     */
    public function handle(Every10minEvent $event)
    {
        DispatchImportOrdersJobs::dispatch();
    }
}
