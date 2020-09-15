<?php

namespace App\Listeners\PickRequest\Deleted;

use App\Services\PicklistService;
use App\Services\PickRequestService;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

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
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        if ($event->getPickRequest()->pick_id === null) {
            return;
        }

        PickRequestService::removeFromPicklist($event->getPickRequest());
    }
}
