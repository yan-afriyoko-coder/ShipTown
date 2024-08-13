<?php

namespace App\Modules\DataCollectorSalePrices\src\Listeners;

use App\Events\DataCollectionRecord\DataCollectionRecordCreatedEvent;
use App\Modules\DataCollectorSalePrices\src\Jobs\AddSalePriceIfApplicable;

class DataCollectionRecordCreatedEventListener
{
    public function handle(DataCollectionRecordCreatedEvent $event): void
    {
        ray('DataCollectionRecordCreatedEvent - Sale Prices');
        AddSalePriceIfApplicable::class::dispatch($event->dataCollectionRecord);
    }
}
