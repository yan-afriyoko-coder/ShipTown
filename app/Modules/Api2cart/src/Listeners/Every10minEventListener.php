<?php

namespace App\Modules\Api2cart\src\Listeners;

use App\Events\Every10minEvent;
use App\Modules\Api2cart\src\Jobs\DispatchImportOrdersJobs;
use App\Modules\Api2cart\src\Jobs\ProcessImportedOrdersJob;
use App\Modules\Api2cart\src\Jobs\UpdateMissingTypeAndIdJob;

class Every10minEventListener
{
    /**
     * Handle the event.
     *
     * @param Every10minEvent $event
     *
     * @return void
     */
    public function handle(Every10minEvent $event)
    {
        UpdateMissingTypeAndIdJob::dispatch();

        DispatchImportOrdersJobs::dispatch();

        ProcessImportedOrdersJob::dispatch();
    }
}
