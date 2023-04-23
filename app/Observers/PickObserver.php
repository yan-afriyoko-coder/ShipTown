<?php

namespace App\Observers;

use App\Models\OrderProduct;
use App\Models\OrderProductPick;

class PickObserver
{
    public function deleted($pick)
    {
        OrderProductPick::query()
            ->where('pick_id', $pick->id)
            ->get()
            ->each(function (OrderProductPick $orderProductPick) {
                if ($orderProductPick->quantity_picked > 0) {
                    OrderProduct::query()
                        ->where('id', $orderProductPick->order_product_id)
                        ->decrement('quantity_picked', $orderProductPick->quantity_picked);
                }

                if ($orderProductPick->quantity_skipped_picking > 0) {
                    OrderProduct::query()
                        ->where('id', $orderProductPick->order_product_id)
                        ->decrement('quantity_skipped_picking', $orderProductPick->quantity_skipped_picking);
                }

                $orderProductPick->delete();
            });
    }
}
