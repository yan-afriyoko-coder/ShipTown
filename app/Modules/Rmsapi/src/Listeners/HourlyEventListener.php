<?php

namespace App\Modules\Rmsapi\src\Listeners;

use App\Modules\Rmsapi\src\Jobs\ProcessProductImports;

class HourlyEventListener
{
    /**
     *
     */
    public function handle()
    {
        ProcessProductImports::dispatch();
    }
}
