<?php

namespace App\Modules\Rmsapi\src\Listeners;

use App\Modules\Rmsapi\src\Jobs\ProcessProductImports;

class Every10minEventListener
{
    public function handle()
    {
        ProcessProductImports::dispatch();
    }
}
