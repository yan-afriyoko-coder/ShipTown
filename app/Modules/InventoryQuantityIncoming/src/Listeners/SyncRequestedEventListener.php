<?php

namespace App\Modules\InventoryQuantityIncoming\src\Listeners;

use App\Modules\InventoryQuantityIncoming\src\Jobs\FixIncorrectQuantityIncomingJob;

class SyncRequestedEventListener
{
    public function handle()
    {
        FixIncorrectQuantityIncomingJob::dispatch();
    }
}
