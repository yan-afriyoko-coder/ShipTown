<?php


namespace App\Modules\AutoStatusLayaway\src\Listeners\ActiveOrderCheckEvent;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Modules\AutoStatusLayaway\src\Jobs\SetLayawayStatusJob;

class SetLayawayStatusListener
{
    public function handle(ActiveOrderCheckEvent $event)
    {
        SetLayawayStatusJob::dispatch($event->order)->delay(now()->addMinutes(5));
    }
}
