<?php

namespace App\Modules\Api2cart\src\Listeners;

use App\Events\SyncRequestedEvent;
use App\Modules\Api2cart\src\Jobs\DispatchImportOrdersJobs;

class SyncRequestedEventListener
{
    /**
     * Handle the event.
     *
     * @param SyncRequestedEvent $event
     *
     * @return void
     */
    public function handle(SyncRequestedEvent $event)
    {
        ray(self::class . '.handle');
        DispatchImportOrdersJobs::dispatch();
    }
}
