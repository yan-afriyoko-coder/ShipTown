<?php

namespace App\Observers;

use App\Events\PickPickedEvent;
use App\Models\Pick;

class PickObserver
{
    /**
     * Handle the pick "created" event.
     *
     * @param Pick $pick
     * @return void
     */
    public function created(Pick $pick)
    {
        //
    }

    /**
     * Handle the pick "updated" event.
     *
     * @param Pick $pick
     * @return void
     */
    public function updated(Pick $pick)
    {
        if ($pick->wasJustPicked()) {
            PickPickedEvent::dispatch($pick);
        }
    }

    /**
     * Handle the pick "deleted" event.
     *
     * @param Pick $pick
     * @return void
     */
    public function deleted(Pick $pick)
    {
        //
    }

    /**
     * Handle the pick "restored" event.
     *
     * @param Pick $pick
     * @return void
     */
    public function restored(Pick $pick)
    {
        //
    }

    /**
     * Handle the pick "force deleted" event.
     *
     * @param Pick $pick
     * @return void
     */
    public function forceDeleted(Pick $pick)
    {
        //
    }
}
