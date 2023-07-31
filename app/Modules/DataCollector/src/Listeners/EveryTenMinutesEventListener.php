<?php

namespace App\Modules\DataCollector\src\Listeners;

use App\Modules\DataCollector\src\Jobs\EnsureCorrectlyArchived;

class EveryTenMinutesEventListener
{
    public function handle(): void
    {
        EnsureCorrectlyArchived::dispatch();
    }
}
