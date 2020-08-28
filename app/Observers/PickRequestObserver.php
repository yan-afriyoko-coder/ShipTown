<?php

namespace App\Observers;

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
        $orderProduct = $pickRequest->orderProduct()->first();

        $pick = Pick::firstOrCreate([
            'product_id' => $orderProduct->product_id,
            'sku_ordered' => $orderProduct->sku_ordered,
            'name_ordered' => $orderProduct->name_ordered,
        ], [
            'quantity_required' => 0
        ]);

        $pick->increment('quantity_required', $orderProduct->quantity_ordered);

        $pickRequest->update(['pick_id' => $pick->getKey()]);
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
