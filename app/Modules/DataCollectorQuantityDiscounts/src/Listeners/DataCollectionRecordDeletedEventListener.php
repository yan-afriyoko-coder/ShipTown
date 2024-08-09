<?php

namespace App\Modules\DataCollectorQuantityDiscounts\src\Listeners;

use App\Events\DataCollectionRecord\DataCollectionRecordDeletedEvent;
use App\Modules\DataCollectorQuantityDiscounts\src\Services\QuantityDiscountsService;

class DataCollectionRecordDeletedEventListener
{
    public function handle(DataCollectionRecordDeletedEvent $event): void
    {
        QuantityDiscountsService::dispatchQuantityDiscountJobs($event->dataCollectionRecord);
    }
}
