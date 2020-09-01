<?php

namespace App\Listeners\Order\StatusChanged;

use App\Events\Order\StatusChangedEvent;
use App\Services\PicklistService;

class AddToOldPicklistListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param StatusChangedEvent $event
     * @return void
     */
    public function handle(StatusChangedEvent $event)
    {
        if ($event->order->status_code == 'picking') {
            PicklistService::addOrderProductToOldPicklist(
                $event->order->orderProducts()->get()->toArray()
            );
        }
    }
}
