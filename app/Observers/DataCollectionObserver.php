<?php

namespace App\Observers;

use App\Events\DataCollection\DataCollectionCreatedEvent;
use App\Events\DataCollection\DataCollectionDeletedEvent;
use App\Events\DataCollection\DataCollectionUpdatedEvent;
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
