<?php

namespace App\Modules\DataCollector\src\Listeners;

use App\Events\SyncRequestedEvent;
use App\Modules\DataCollector\src\Jobs\EnsureCorrectlyArchived;

class SyncRequestedEventListener
{
    public function handle(SyncRequestedEvent $event): void
    {
        EnsureCorrectlyArchived::dispatch();
    }
}
