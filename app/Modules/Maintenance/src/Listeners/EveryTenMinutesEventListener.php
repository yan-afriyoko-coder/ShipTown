<?php

namespace App\Modules\Maintenance\src\Listeners;

use App\Modules\Maintenance\src\Jobs\temp\FillPreviousMovementIdJob;

class EveryTenMinutesEventListener
{
    public function handle()
    {
        FillPreviousMovementIdJob::dispatch();
    }
}
