<?php

namespace App\Modules\OrderTotals\src\Listeners;

use App\Modules\OrderTotals\src\Jobs\EnsureAllRecordsExistsJob;
use App\Modules\OrderTotals\src\Jobs\EnsureCorrectTotalsJob;
use App\Modules\OrderTotals\src\Jobs\UpdateOrderTotalsJob;

class DispatchAllJobs
{
    public function handle()
    {
         EnsureAllRecordsExistsJob::dispatch();
         EnsureCorrectTotalsJob::dispatch();
         UpdateOrderTotalsJob::dispatch();
    }
}
