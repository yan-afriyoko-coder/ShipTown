<?php

namespace App\Observers;

use App\Events\DataCollectionCreatedEvent;
use App\Events\DataCollectionDeletedEvent;
use App\Events\DataCollectionUpdatedEvent;
use App\Models\DataCollection;

class DataCollectionObserver
{
    public function created(DataCollection $dataCollection)
    {
        DataCollectionCreatedEvent::dispatch($dataCollection);
    }

    public function updated(DataCollection $dataCollection)
    {
        DataCollectionUpdatedEvent::dispatch($dataCollection);
    }

    public function deleted(DataCollection $dataCollection)
    {
        DataCollectionDeletedEvent::dispatch($dataCollection);
    }
}
