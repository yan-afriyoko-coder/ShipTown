<?php

namespace App\Modules\InventoryQuantityIncoming\src\Listeners;

use App\Events\DataCollectionRecordCreatedEvent;
use App\Models\DataCollectionTransferIn;
use App\Modules\InventoryQuantityIncoming\src\Jobs\FixIncorrectQuantityIncomingJob;

class DataCollectionRecordCreatedEventListener
{
    public function handle(DataCollectionRecordCreatedEvent $event)
    {
        $record = $event->dataCollectionRecord;

        if ($record->dataCollection->type === DataCollectionTransferIn::class) {
            FixIncorrectQuantityIncomingJob::dispatchNow($record->product_id, $record->dataCollection->warehouse_id);
        }
    }
}
