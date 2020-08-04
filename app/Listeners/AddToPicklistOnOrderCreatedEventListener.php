<?php

namespace App\Listeners;

use App\Events\OrderCreatedEvent;
use App\Models\Order;
use App\Models\Picklist;
use App\Services\PicklistService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class AddToPicklistOnOrderCreatedEventListener
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
        if ($order->status_code == 'picking') {
            PicklistService::addOrderProductPick(
                $order->orderProducts()->get()->toArray()
            );
        }
    }
}
