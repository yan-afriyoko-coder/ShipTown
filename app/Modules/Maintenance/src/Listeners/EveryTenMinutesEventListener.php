<?php

namespace App\Modules\Maintenance\src\Listeners;

use App\Modules\Maintenance\src\Jobs\temp\FillPreviousMovementIdJob;
use App\Modules\Maintenance\src\Jobs\temp\FixIncorrectQuantityBeforeAndAfterJob;

class EveryTenMinutesEventListener
{
    public function handle()
    {
        FillPreviousMovementIdJob::dispatch();
//        FixIncorrectQuantityBeforeAndAfterJob::dispatch();
    }
}
