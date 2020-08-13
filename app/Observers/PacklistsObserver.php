<?php

namespace App\Observers;

use App\Models\Order;
use App\Models\Packlist;

class PacklistsObserver
{
    /**
     * Handle the packlist "created" event.
     *
     * @param Packlist $packlist
     * @return void
     */
    public function created(Packlist $packlist)
    {
        //
    }

    /**
     * Handle the packlist "updated" event.
     *
     * @param  Packlist  $packlist
     * @return void
     */
    public function updated(Packlist $packlist)
    {
        if( ! Packlist::query()
            ->where(['order_id' => $packlist->order_id])
            ->whereNull('packed_at')
            ->exists())
        {
            Order::query()->where(['id' => $packlist->order_id])->update(['packed_at' => now()]);
        }
    }

    /**
     * Handle the packlist "deleted" event.
     *
     * @param  Packlist  $packlist
     * @return void
     */
    public function deleted(Packlist $packlist)
    {
        //
    }

    /**
     * Handle the packlist "restored" event.
     *
     * @param  Packlist  $packlist
     * @return void
     */
    public function restored(Packlist $packlist)
    {
        //
    }

    /**
     * Handle the packlist "force deleted" event.
     *
     * @param  Packlist  $packlist
     * @return void
     */
    public function forceDeleted(Packlist $packlist)
    {
        //
    }
}
