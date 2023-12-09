<?php

namespace Database\Seeders\Demo;

use App\Models\Order;
use App\Models\OrderProduct;
use Illuminate\Database\Seeder;

class CollectionOrdersSeeder extends Seeder
{
    public function run()
    {
        $orders = Order::factory(4)->create([
            'status_code' => 'store_collection',
            'shipping_method_code' => 'store_collection',
            'shipping_method_name' => 'store_collection',
            'label_template' => 'address_label',
        ]);

        $orders->each(function (Order $order) {
            OrderProduct::factory(2)->create([
                'order_id' => $order->getKey(),
            ]);

            $order = $order->refresh();
            $order->update(['total_paid' => $order->total_order]);
        });
    }
}
