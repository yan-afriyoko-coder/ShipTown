<?php

namespace App\Listeners\Pick\Unpicked;

use App\Models\PickRequest;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

class ClearPickRequestsQuantityPickedListener
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
        PickRequest::query()
            ->where(['pick_id' => $event->getPick()->getKey()])
            ->update(['quantity_picked' => 0]);
    }
}
