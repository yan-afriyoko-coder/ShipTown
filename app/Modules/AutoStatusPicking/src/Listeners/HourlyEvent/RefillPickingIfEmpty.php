<?php

namespace App\Modules\AutoStatusPicking\src\Listeners\HourlyEvent;

use App\Modules\AutoStatusPicking\src\Jobs\RefillPickingIfEmptyJob;

/**
 * Class SetPackingWebStatus.
 */
class RefillPickingIfEmpty
{
    public function handle(): void
    {
        RefillPickingIfEmptyJob::dispatch();
    }
}
