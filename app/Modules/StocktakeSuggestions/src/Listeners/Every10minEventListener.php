<?php

namespace App\Modules\StocktakeSuggestions\src\Listeners;

use App\Events\Every10minEvent;
use App\Modules\StocktakeSuggestions\src\Jobs\RunAllJobsJob;

class Every10minEventListener
{
    public function handle(Every10minEvent $event)
    {
        RunAllJobsJob::dispatch();
    }
}
