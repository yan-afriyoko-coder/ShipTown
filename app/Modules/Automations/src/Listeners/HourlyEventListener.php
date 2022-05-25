<?php

namespace App\Modules\Automations\src\Listeners;

use App\Modules\Automations\src\Jobs\RunAutomationsOnActiveOrdersJob;

class HourlyEventListener
{
    public function handle()
    {
        RunAutomationsOnActiveOrdersJob::dispatch();
    }
}
