<?php


namespace App\Modules\AutoStatusLayawayStorePickup\src\Listeners;

use App\Events\Order\OrderUpdatedEvent;
use App\Modules\AutoStatusLayaway\src\Jobs\SetLayawayStatusJob;

class OrderUpdatedListener
{
    public function handle(OrderUpdatedEvent $event)
    {
        if ($event->order->status_code === 'paid') {
            $event->order->update(['status_code' => 'layaway']);
        }
    }
}
