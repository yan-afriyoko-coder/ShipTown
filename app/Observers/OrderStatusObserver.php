<?php


namespace App\Observers;

use App\Models\Order;
use App\Models\OrderStatus;

class OrderStatusObserver
{
    public function updated(OrderStatus $orderStatus)
    {
        if ($orderStatus->isAttributeChanged('order_active')) {
            $orders = Order::where(['status_code' => $orderStatus->code])->get();

            $orders->each(function (Order $order) use ($orderStatus) {
                $order->update(['is_active' => $orderStatus->order_active]);
            });
        }
    }
}
