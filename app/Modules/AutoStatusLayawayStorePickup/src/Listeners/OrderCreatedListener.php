<?php


namespace App\Modules\AutoStatusLayawayStorePickup\src\Listeners;

use App\Events\Order\OrderCreatedEvent;

class OrderCreatedListener
{
    public function handle(OrderCreatedEvent $event)
    {
        if (($event->order->status_code === 'paid') and ($event->order->shipping_method_code <> "tablerate_bestway")) {
            $event->order->log('Store pickup detected, changing status to layaway');
            $event->order->update(['status_code' => 'layaway']);
        }
    }
}
