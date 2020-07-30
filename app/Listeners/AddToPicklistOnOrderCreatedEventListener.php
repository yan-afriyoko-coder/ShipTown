<?php

namespace App\Listeners;

use App\Events\OrderCreatedEvent;
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
        if ($event->order->status_code == 'picking') {
            PicklistService::addOrderProductPick(
                $event->order->orderProducts()->get()->toArray()
            );
        }
    }
}
