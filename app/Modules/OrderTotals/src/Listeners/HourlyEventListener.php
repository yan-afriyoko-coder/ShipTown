<?php

namespace App\Modules\OrderTotals\src\Listeners;

use App\Modules\OrderTotals\src\Jobs\EnsureCorrectTotalsJob;

class HourlyEventListener
{
    public function handle(HourlyEventListener $event)
    {
        EnsureCorrectTotalsJob::dispatch();
    }
}
