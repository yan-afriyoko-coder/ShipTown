<?php

namespace App\Modules\InventoryMovementsStatistics\src\Listeners;

use App\Events\SyncRequestedEvent;
use App\Modules\InventoryMovementsStatistics\src\Jobs\ClearOutdatedStatisticsJob;
use App\Modules\InventoryMovementsStatistics\src\Jobs\EnsureCorrectLastSoldAtJob;
use App\Modules\InventoryMovementsStatistics\src\Jobs\RepopulateLast28DaysTableJob;
use App\Modules\InventoryMovementsStatistics\src\Jobs\UpdateInventoryMovementsStatisticsTableJob;

class SyncRequestedEventListener
{
    public function handle(SyncRequestedEvent $event)
    {
        RepopulateLast28DaysTableJob::dispatch();
        EnsureCorrectLastSoldAtJob::dispatch();
        UpdateInventoryMovementsStatisticsTableJob::dispatch();
        ClearOutdatedStatisticsJob::dispatch();
    }
}
