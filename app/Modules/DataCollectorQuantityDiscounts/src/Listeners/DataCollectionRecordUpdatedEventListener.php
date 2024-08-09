<?php

namespace App\Modules\DataCollectorQuantityDiscounts\src\Listeners;

use App\Events\DataCollectionRecord\DataCollectionRecordUpdatedEvent;
use App\Modules\DataCollectorQuantityDiscounts\src\Services\QuantityDiscountsService;

class DataCollectionRecordUpdatedEventListener
{
    public function handle(DataCollectionRecordUpdatedEvent $event): void
    {
        QuantityDiscountsService::dispatchQuantityDiscountJobs($event->dataCollectionRecord);
    }
}
