<?php

namespace App\Modules\InventoryTotals\src\Listeners;

use App\Modules\InventoryTotals\src\Jobs\LastCountedAtJob;

class SyncRequestedEventListener
{
    public function handle()
    {
        LastCountedAtJob::dispatch();
    }
}
