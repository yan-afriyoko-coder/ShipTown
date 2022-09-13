<?php

namespace App\Modules\Api2cart\src\Listeners;

use App\Modules\Api2cart\src\Jobs\UpdateMissingTypeAndIdJob;

class SyncRequestedEventListener
{
    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle()
    {
        UpdateMissingTypeAndIdJob::dispatch();
    }
}
