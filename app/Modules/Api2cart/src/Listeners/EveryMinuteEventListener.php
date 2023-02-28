<?php

namespace App\Modules\Api2cart\src\Listeners;

use App\Events\EveryMinuteEvent;
use App\Modules\Api2cart\src\Jobs\DispatchImportOrdersJobs;
use App\Modules\Api2cart\src\Jobs\ProcessImportedOrdersJob;

class EveryMinuteEventListener
{
    public function handle(EveryMinuteEvent $event)
    {
        DispatchImportOrdersJobs::dispatch();
        ProcessImportedOrdersJob::dispatch();
    }
}
