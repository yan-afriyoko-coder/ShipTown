<?php


namespace App\Modules\AutoStatusLayawayStorePickup\src\Listeners;

use App\Events\Order\ActiveOrderCheckEvent;
use App\Modules\AutoStatusLayaway\src\Jobs\SetLayawayStatusJob;

class ActiveOrderCheckListener
{
    public function handle(ActiveOrderCheckEvent $event)
    {
        if (($event->order->status_code === 'paid') and ($event->order->shipping_method_code <> "tablerate_bestway")) {
            $event->order->log('Store pickup detected, changing status to layaway');
            $event->order->update(['status_code' => 'layaway']);
        }
    }
}
