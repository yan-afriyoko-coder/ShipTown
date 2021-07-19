<?php


namespace App\Modules\AutoStatusLayaway\src\Listeners\ActiveOrderCheckEvent;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Modules\AutoStatusLayaway\src\Jobs\SetLayawayStatusJob;
use Illuminate\Contracts\Queue\ShouldQueue;

class SetLayawayStatusListener implements ShouldQueue
{
    public function handle(ActiveOrderCheckEvent $event)
    {
        SetLayawayStatusJob::dispatchNow($event->getOrder());
    }
}
