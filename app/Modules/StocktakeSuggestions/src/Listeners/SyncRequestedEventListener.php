<?php

namespace App\Modules\StocktakeSuggestions\src\Listeners;

use App\Events\SyncRequestedEvent;
use App\Modules\StocktakeSuggestions\src\Jobs\RunAllJobsJob;

class SyncRequestedEventListener
{
    public function handle(SyncRequestedEvent $event)
    {
        RunAllJobsJob::dispatch();
    }
}
