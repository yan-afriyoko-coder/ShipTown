<?php

namespace App\Modules\_SampleModuleStructure\src\Listeners;

use App\Events\DailyEvent;

class DailyEventListener
{
    public function handle(DailyEvent $event): bool
    {
        //

        return true;
    }
}
