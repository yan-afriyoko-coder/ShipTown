<?php

namespace App\Modules\NonInventoryProductTag\src\Listeners;

use App\Events\SyncRequestedEvent;
use App\Modules\NonInventoryProductTag\src\Jobs\ClearArcadiaStockJob;

class SyncRequestedEventListener
{
    public function handle(SyncRequestedEvent $event)
    {
        ClearArcadiaStockJob::dispatch();
    }
}
