<?php

namespace App\Modules\DataCollectorGroupRecords\src\Listeners;

use App\Events\DataCollection\DataCollectionRecalculateRequestEvent;
use App\Modules\DataCollectorGroupRecords\src\Jobs\GroupRecordsJob;

class DataCollectionRecalculateRequestEventListener
{
    public function handle(DataCollectionRecalculateRequestEvent $event): void
    {
        GroupRecordsJob::dispatchSync($event->dataCollection);
    }
}
