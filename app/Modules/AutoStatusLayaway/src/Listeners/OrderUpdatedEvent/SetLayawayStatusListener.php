<?php


namespace App\Modules\AutoStatusLayaway\src\Listeners\OrderUpdatedEvent;

use App\Events\Order\OrderUpdatedEvent;
use App\Modules\AutoStatusLayaway\src\Jobs\SetLayawayStatusJob;
use Illuminate\Contracts\Queue\ShouldQueue;

class SetLayawayStatusListener implements ShouldQueue
{
    public function handle(OrderUpdatedEvent $event)
    {
        SetLayawayStatusJob::dispatchNow($event->getOrder());
    }
}
