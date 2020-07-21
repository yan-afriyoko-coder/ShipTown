<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\Picklist;

class PicklistsObserver
{
    /**
     * Handle the picklists "created" event.
     *
     * @param  Picklist $picklist
     * @return void
     */
    public function created(Picklist $picklist)
    {
        //
    }

    /**
     * Handle the picklists "updated" event.
     *
     * @param  Picklist $picklist
     * @return void
     */
    public function updated(Picklist $picklist)
    {
        if( ! Picklist::query()
            ->where(['order_id' => $picklist->order_id])
            ->whereNull('picked_at')
            ->exists())
        {
            Order::query()->where(['id' => $picklist->order_id])->update(['is_picked' => true]);
        }
    }

    /**
     * Handle the picklists "deleted" event.
     *
     * @param  Picklist $picklist
     * @return void
     */
    public function deleted(Picklist $picklist)
    {
        //
    }

    /**
     * Handle the picklists "restored" event.
     *
     * @param  Picklist $picklist
     * @return void
     */
    public function restored(Picklist $picklist)
    {
        //
    }

    /**
     * Handle the picklists "force deleted" event.
     *
     * @param  Picklist $picklist
     * @return void
     */
    public function forceDeleted(Picklist $picklist)
    {
        //
    }
}
