<?php

namespace App\Listeners\Order\Created;

use App\Events\Order\CreatedEvent;
use App\Models\Order;
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
     * @param CreatedEvent $event
     * @return void
     */
    public function handle(CreatedEvent $event)
    {
        $this->addToPicklist($event->order);
    }

    /**
     * @param Order $order
     */
    public function addToPicklist(Order $order): void
    {
        if ($order->status_code === 'picking') {
            PicklistService::addOrderProductPick(
                $order->orderProducts()->get()->toArray()
            );
        }
    }
}
