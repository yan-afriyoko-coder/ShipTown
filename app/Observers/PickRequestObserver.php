<?php

namespace App\Observers;

use App\Events\PickRequestCreatedEvent;
use App\Models\Pick;
use App\Models\PickRequest;

class PickRequestObserver
{
    /**
     * Handle the pick request "created" event.
     *
     * @param PickRequest $pickRequest
     * @return void
     */
    public function created(PickRequest $pickRequest)
    {
        PickRequestCreatedEvent::dispatch($pickRequest);
    }

    /**
     * Handle the pick request "updated" event.
     *
     * @param PickRequest $pickRequest
     * @return void
     */
    public function updated(PickRequest $pickRequest)
    {
        //
    }

    /**
     * Handle the pick request "deleted" event.
     *
     * @param PickRequest $pickRequest
     * @return void
     */
    public function deleted(PickRequest $pickRequest)
    {
        //
    }

    /**
     * Handle the pick request "restored" event.
     *
     * @param PickRequest $pickRequest
     * @return void
     */
    public function restored(PickRequest $pickRequest)
    {
        //
    }

    /**
     * Handle the pick request "force deleted" event.
     *
     * @param PickRequest $pickRequest
     * @return void
     */
    public function forceDeleted(PickRequest $pickRequest)
    {
        //
    }
}
