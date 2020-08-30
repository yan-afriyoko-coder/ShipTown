<?php

namespace App\Listeners\PickPickedEvent;

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
        PickRequest::query()
            ->where(['pick_id' => $event->getPick()->getKey()])
            ->update(['quantity_picked' => DB::raw('quantity_required')]);
    }
}
