<?php

namespace App\Modules\Api2cart\src\Listeners;

use App\Events\Every10MinuteEvent;
use App\Modules\Api2cart\src\Jobs\DispatchImportOrdersJobs;
use App\Modules\Api2cart\src\Jobs\ProcessImportedOrdersJob;

class EveryMinuteEventListener
{
    public function handle(Every10MinuteEvent $event)
    {
        DispatchImportOrdersJobs::dispatch();
        ProcessImportedOrdersJob::dispatch();
    }
}
