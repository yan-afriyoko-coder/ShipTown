<?php

namespace App\Observers;

use App\Events\DataCollectionRecord\DataCollectionRecordCreatedEvent;
use App\Events\DataCollectionRecord\DataCollectionRecordDeletedEvent;
use App\Events\DataCollectionRecord\DataCollectionRecordUpdatedEvent;
use App\Models\DataCollectionRecord;

class DataCollectionRecordObserver
{
    public function created(DataCollectionRecord $dataCollectionRecord)
    {
        DataCollectionRecordCreatedEvent::dispatch($dataCollectionRecord);
    }

    public function updated(DataCollectionRecord $dataCollectionRecord)
    {
        DataCollectionRecordUpdatedEvent::dispatch($dataCollectionRecord);
    }

    public function deleted(DataCollectionRecord $dataCollectionRecord)
    {
        DataCollectionRecordDeletedEvent::dispatch($dataCollectionRecord);
    }
}
