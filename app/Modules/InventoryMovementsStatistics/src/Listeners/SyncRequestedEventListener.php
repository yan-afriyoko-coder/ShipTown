<?php

namespace App\Modules\InventoryMovementsStatistics\src\Listeners;

use App\Events\SyncRequestedEvent;
use App\Modules\InventoryMovementsStatistics\src\Jobs\RepopulateLast28DaysTableJob;

class SyncRequestedEventListener
{
    public function handle(SyncRequestedEvent $event)
    {
        RepopulateLast28DaysTableJob::dispatch();
    }
}
