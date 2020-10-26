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
        if ($event->getPickRequest()->pick_id === null) {
            return;
        }

        $event->getPickRequest()
            ->orderProduct()
            ->first()
            ->update(['quantity_not_picked' => $event->getPickRequest()->quantity_required]);

        PickRequestService::removeFromPicklist($event->getPickRequest());
    }
}
