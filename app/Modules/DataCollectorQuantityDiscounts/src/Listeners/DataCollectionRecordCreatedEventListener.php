<?php

namespace App\Modules\DataCollectorQuantityDiscounts\src\Listeners;

use App\Events\DataCollectionRecord\DataCollectionRecordCreatedEvent;
use App\Modules\DataCollectorQuantityDiscounts\src\Services\QuantityDiscountsService;

class DataCollectionRecordCreatedEventListener
{
    public function handle(DataCollectionRecordCreatedEvent $event): void
    {
        QuantityDiscountsService::dispatchQuantityDiscountJobs($event->dataCollectionRecord);
    }
}
