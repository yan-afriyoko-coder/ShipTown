<?php

namespace Database\Seeders\Demo;

use App\Models\Order;
use App\Models\OrderProduct;
use Illuminate\Database\Seeder;

class OrderProductNotExistsSeeder extends Seeder
{
    public function run()
    {
        $order = Order::factory()->create(['order_number' => 'T100001', 'status_code' => 'paid']);

        OrderProduct::factory()->create([
            'order_id' => $order->getKey(),
            'sku_ordered' => '123123123123123',
            'quantity_ordered' => 1,
            'product_id' => null,
        ]);
    }
}
