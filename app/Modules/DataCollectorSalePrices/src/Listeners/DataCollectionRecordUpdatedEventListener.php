<?php

namespace App\Modules\DataCollectorSalePrices\src\Listeners;

use App\Events\DataCollectionRecord\DataCollectionRecordUpdatedEvent;
use App\Modules\DataCollectorSalePrices\src\Jobs\AddSalePriceIfApplicable;

class DataCollectionRecordUpdatedEventListener
{
    public function handle(DataCollectionRecordUpdatedEvent $event): void
    {
        AddSalePriceIfApplicable::class::dispatch($event->dataCollectionRecord);
    }
}
