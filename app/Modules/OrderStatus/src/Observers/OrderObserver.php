<?php

namespace App\Modules\OrderStatus\src\Observers;

use App\Models\Order;
use App\Models\OrderStatus;

class OrderObserver
{
    /**
     * @param Order $order
     */
    public function creating(Order $order)
    {
        OrderStatus::firstOrCreate(['code' => $order->status_code], ['name' => $order->status_code]);
    }

    /**
     * @param Order $order
     */
    public function updating(Order $order)
    {
        if ($order->isAttributeNotChanged('status_code')) {
            return;
        }

        OrderStatus::firstOrCreate(['code' => $order->status_code], ['name' => $order->status_code]);
    }
}
