<?php

namespace App\Modules\Picklist\src\Listeners;

use App\Modules\Picklist\src\Jobs\DistributePicksJob;

class PickCreatedListener
{
    public function handle($event): void
    {
        DistributePicksJob::dispatch($event->pick);
    }
}
