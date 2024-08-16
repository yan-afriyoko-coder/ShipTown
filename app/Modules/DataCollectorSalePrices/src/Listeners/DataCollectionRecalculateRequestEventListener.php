<?php

namespace App\Modules\DataCollectorSalePrices\src\Listeners;

use App\Events\DataCollection\DataCollectionRecalculateRequestEvent;
use App\Modules\DataCollectorSalePrices\src\Jobs\AddSalePriceIfApplicable;

class DataCollectionRecalculateRequestEventListener
{
    public function handle(DataCollectionRecalculateRequestEvent $event): void
    {
        AddSalePriceIfApplicable::dispatch($event->dataCollection);
    }
}
