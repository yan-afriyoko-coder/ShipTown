<?php

namespace Database\Seeders\Demo;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Pick;
use App\User;
use Illuminate\Database\Seeder;

class PaidPickedOrdersSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

         $orders = Order::factory()->count(10)->create();
         $orders->each(function (Order $order) use ($user) {
             $orderProducts = OrderProduct::factory()->count(3)->make();

             $order->orderProducts()->saveMany($orderProducts);
             $orderProducts->each(function (OrderProduct $orderProduct) use ($order, $user) {
                 $orderProduct->update(['quantity_picked' => $orderProduct->quantity_ordered]);

                 Pick::query()->create([
                     'user_id' => $user->getKey(),
                     'warehouse_code' => $user->warehouse->code,
                     'product_id' => $orderProduct->product_id,
                     'sku_ordered' => $orderProduct->sku_ordered,
                     'name_ordered' => $orderProduct->name_ordered,
                     'quantity_picked' => $orderProduct->quantity_ordered,
                     'quantity_skipped_picking' => 0,
                 ]);
             });
         });
    }
}
