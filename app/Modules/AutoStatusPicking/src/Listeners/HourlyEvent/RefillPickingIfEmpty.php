<?php

namespace App\Modules\AutoStatusPicking\src\Listeners\HourlyEvent;

use App\Events\HourlyEvent;
use App\Modules\AutoStatusPicking\src\Jobs\RefillPickingIfEmptyJob;

/**
 * Class SetPackingWebStatus.
 */
class RefillPickingIfEmpty
{
    /**
     * Handle the event.
     *
     * @param HourlyEvent $event
     *
     * @return void
     */
    public function handle(HourlyEvent $event)
    {
        RefillPickingIfEmptyJob::dispatch();
    }
}
