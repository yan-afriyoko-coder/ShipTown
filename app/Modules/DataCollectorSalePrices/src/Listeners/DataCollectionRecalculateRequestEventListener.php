<?php

namespace App\Modules\DataCollectorSalePrices\src\Listeners;

use App\Events\DataCollection\DataCollectionRecalculateRequestEvent;
use App\Models\DataCollectionTransaction;
use App\Modules\DataCollectorSalePrices\src\Jobs\ApplySalePricesJob;

class DataCollectionRecalculateRequestEventListener
{
    public function handle(DataCollectionRecalculateRequestEvent $event): void
    {
        if ($event->dataCollection->type !== DataCollectionTransaction::class) {
            return;
        }

        ApplySalePricesJob::dispatch($event->dataCollection);
    }
}
