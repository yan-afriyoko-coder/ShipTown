<?php

namespace App\Modules\Automations\src\Listeners;

use App\Modules\Automations\src\Jobs\RunEnabledAutomationsJob;

class EveryTenMinutesEventListener
{
    public function handle()
    {
        RunEnabledAutomationsJob::dispatch();
    }
}
