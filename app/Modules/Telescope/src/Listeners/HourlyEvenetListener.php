<?php

namespace App\Modules\Telescope\src\Listeners;

use App\Events\DailyEvent;
use App\Modules\Telescope\src\Jobs\PruneEntriesJob;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class HourlyEvenetListener
{
    public function handle(DailyEvent $event): bool
    {
        PruneEntriesJob::dispatch();

        return true;
    }
}
