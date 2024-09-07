<?php

namespace App\Modules\MagentoApi\src\Listeners;

use App\Modules\MagentoApi\src\Jobs\CheckIfSyncIsRequiredJob;

class EveryMinuteEventListener
{
    public function handle(): void
    {
        CheckIfSyncIsRequiredJob::dispatch();
    }
}
