<?php


namespace App\Modules\AutoStatusLayaway\src\Listeners\OrderUpdatedEvent;

use App\Events\Order\OrderUpdatedEvent;

class SetLayawayStatusListener
{
    public function handle(OrderUpdatedEvent $event)
    {
        if ($event->getOrder()->status_code === 'paid') {
            $event->getOrder()->update(['status_code' => 'layaway']);
        }
    }
}
