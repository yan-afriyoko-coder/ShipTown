<?php

namespace App\Modules\OrderTotals\src\Listeners;

use App\Events\HourlyEvent;
use App\Modules\OrderTotals\src\Jobs\EnsureAllRecordsExistsJob;
use App\Modules\OrderTotals\src\Jobs\EnsureCorrectTotalsJob;

class HourlyEventListener
{
    public function handle(HourlyEvent $event)
    {
        EnsureAllRecordsExistsJob::dispatch();
        EnsureCorrectTotalsJob::dispatch();
    }
}
