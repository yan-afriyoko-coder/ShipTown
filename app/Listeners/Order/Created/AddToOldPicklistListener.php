<?php

namespace App\Listeners\Order\Created;

use App\Events\Order\OrderCreatedEvent;
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
     * @param OrderCreatedEvent $event
     * @return void
     */
    public function handle(OrderCreatedEvent $event)
    {
        $this->addToPicklist($event->order);
    }

    /**
     * @param Order $order
     */
    public function addToPicklist(Order $order): void
    {
        if ($order->status_code === 'picking') {
            PicklistService::addOrderProductToOldPicklist(
                $order->orderProducts()->get()->toArray()
            );
        }
    }
}
