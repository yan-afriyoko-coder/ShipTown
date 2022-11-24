<?php

namespace App\Modules\InventoryQuantityIncoming\src\Listeners;

use App\Modules\InventoryQuantityIncoming\src\Jobs\FixIncorrectQuantityIncomingJob;

class Every10minEventListener
{
    public function handle()
    {
        FixIncorrectQuantityIncomingJob::dispatch();
    }
}
