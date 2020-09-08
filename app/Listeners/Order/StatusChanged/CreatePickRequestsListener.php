<?php

namespace App\Listeners\Order\StatusChanged;

use App\Events\Order\StatusChangedEvent;
use App\Models\Order;
use App\Models\PickRequest;
use App\Services\OrderService;

class CreatePickRequestsListener
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
        if ($event->isStatusCode('picking')) {
            OrderService::createPickRequests($event->getOrder());
        }
    }
}
