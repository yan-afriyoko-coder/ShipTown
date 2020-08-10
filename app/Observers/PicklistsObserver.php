<?php

namespace App\Observers;

use Illuminate\Support\Carbon;

use App\Models\Order;
use App\Models\Picklist;
use App\Notifications\PicklistProductMissing;

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

        if ($picklist->wasPickSkipped()) {
            $user = $picklist->user;

            if ($user) {
                $user->notifyAt(new PicklistProductMissing($picklist), Carbon::now()->addMinutes(5));
            }
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
