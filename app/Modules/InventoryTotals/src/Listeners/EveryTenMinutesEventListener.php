<?php

namespace App\Modules\InventoryTotals\src\Listeners;

use App\Modules\InventoryTotals\src\Jobs\LastCountedAtJob;

class EveryTenMinutesEventListener
{
    public function handle()
    {
        LastCountedAtJob::dispatch();
    }
}
