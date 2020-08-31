<?php

namespace App\Listeners\PickRequest\Created;

use App\Events\PickRequestCreatedEvent;
use App\Models\Pick;
use App\Services\PicklistService;

class AddQuantityToPicklistListener
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
     * @param PickRequestCreatedEvent $event
     * @return void
     */
    public function handle(PickRequestCreatedEvent $event)
    {
        if ($event->getPickRequest()->pick_id === null) {
            PicklistService::addToPicklist($event->getPickRequest());
        }
    }
}
