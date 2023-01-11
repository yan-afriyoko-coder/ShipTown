<?php

namespace App\Modules\DataCollector\src\Listeners;

use App\Events\Every10minEvent;
use App\Modules\DataCollector\src\Jobs\EnsureAllTransferredOutJob;

class Every10minEventListener
{
    public function handle(Every10minEvent $event): void
    {
        EnsureAllTransferredOutJob::dispatch();
    }
}
