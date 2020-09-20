<?php

namespace App\Listeners\PickRequest\Deleted;

use App\Events\PickRequest\DeletedEvent;
use App\Services\PickRequestService;

class RemoveQuantityFromPicklistListener
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
     * @param DeletedEvent $event
     * @return void
     */
    public function handle(DeletedEvent $event)
    {
//        $event->getPickRequest()->orderProduct()->first()->order()->first()->update(['status_code' => 'missing_item']);

        if ($event->getPickRequest()->pick_id === null) {
            return;
        }

        PickRequestService::removeFromPicklist($event->getPickRequest());
    }
}
