<?php

namespace App\Listeners\Pick\Picked;

use App\Events\PickPickedEvent;
use App\Models\PickRequest;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

class FillPickRequestsPickedQuantityListener
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
     * @param PickPickedEvent $event
     * @return void
     */
    public function handle(PickPickedEvent $event)
    {
        $pickRequests = PickRequest::query()
            ->where(['pick_id' => $event->getPick()->getKey()])
            ->get();

        foreach ($pickRequests as $pickRequest) {
            $pickRequest->update(['quantity_picked' => $pickRequest->quantity_required]);
        }
    }
}
