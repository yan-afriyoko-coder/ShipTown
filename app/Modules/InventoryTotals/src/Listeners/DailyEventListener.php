<?php

namespace App\Modules\InventoryTotals\src\Listeners;

use App\Modules\InventoryTotals\src\Jobs\CheckForIncorrectInventoryQuantityJob;

class DailyEventListener
{
    public function handle()
    {
        CheckForIncorrectInventoryQuantityJob::dispatch();
    }
}
