<?php

namespace App\Observers;

use App\Events\PickPickedEvent;
use App\Events\PickUnpickedEvent;
use App\Models\Pick;

/**
 * Class PickObserver
 * @package App\Observers
 */
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
        $this->dispatchPickedEvents($pick);
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

    /**
     * @param Pick $pick
     */
    private function dispatchPickedEvents(Pick $pick): void
    {
        if ($pick->isAttributeValueChanged('picked_at')) {
            if ($pick->is_picked) {
                PickPickedEvent::dispatch($pick);
            } else {
                PickUnpickedEvent::dispatch($pick);
            }
        }
    }
}
