<?php


namespace App\Modules\AutoStatusLayaway\src\Listeners\ActiveOrderCheckEvent;

use App\Events\Order\ActiveOrderCheckEvent;

class SetLayawayStatusListener
{
    public function handle(ActiveOrderCheckEvent $event)
    {
        if ($event->getOrder()->status_code === 'paid') {
            $event->getOrder()->update(['status_code' => 'layaway']);
        }
    }
}
