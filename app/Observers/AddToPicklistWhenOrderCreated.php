<?php

namespace App\Observers;

use App\Models\OrderProduct;
use App\Models\Picklist;

class AddToPicklistWhenOrderCreated
{
    /**
     * Handle the order product "created" event.
     *
     * @param OrderProduct $orderProduct
     * @return void
     */
    public function created(OrderProduct $orderProduct)
    {
        Picklist::query()->create([
            'product_id' => $orderProduct->product_id,
            'location_id' => 'WWW',
            'quantity_to_pick' => $orderProduct->quantity,
        ]);
    }

    /**
     * Handle the order product "updated" event.
     *
     * @param OrderProduct $orderProduct
     * @return void
     */
    public function updated(OrderProduct $orderProduct)
    {
        //
    }

    /**
     * Handle the order product "deleted" event.
     *
     * @param OrderProduct $orderProduct
     * @return void
     */
    public function deleted(OrderProduct $orderProduct)
    {
        //
    }

    /**
     * Handle the order product "restored" event.
     *
     * @param OrderProduct $orderProduct
     * @return void
     */
    public function restored(OrderProduct $orderProduct)
    {
        //
    }

    /**
     * Handle the order product "force deleted" event.
     *
     * @param OrderProduct $orderProduct
     * @return void
     */
    public function forceDeleted(OrderProduct $orderProduct)
    {
        //
    }
}
