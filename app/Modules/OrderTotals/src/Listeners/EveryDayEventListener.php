<?php

namespace App\Modules\OrderTotals\src\Listeners;

use App\Modules\OrderTotals\src\Jobs\EnsureAllRecordsExistsJob;
use App\Modules\OrderTotals\src\Jobs\EnsureCorrectTotalsJob;

class EveryDayEventListener
{
    public function handle()
    {
        EnsureAllRecordsExistsJob::dispatch();
        EnsureCorrectTotalsJob::dispatch();
    }
}
