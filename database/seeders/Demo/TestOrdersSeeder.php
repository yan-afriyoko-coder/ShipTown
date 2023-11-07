<?php

namespace Database\Seeders\Demo;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use Illuminate\Database\Seeder;

class TestOrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $product1 = Product::query()->firstOrCreate(['sku' => '45'], ['name' => 'Test Product - 45']);
        $product2 = Product::query()->firstOrCreate(['sku' => '44'], ['name' => 'Test Product - 44']);

        $order = Order::factory()->create(['order_number' => 'T100001 - Packsheet', 'status_code' => 'paid']);

        OrderProduct::factory()->create([
            'order_id' => $order->getKey(),
            'quantity_ordered' => 1,
            'product_id' => $product1->getKey(),
        ]);

        OrderProduct::factory()->create([
            'order_id' => $order->getKey(),
            'quantity_ordered' => 2,
            'product_id' => $product2->getKey(),
        ]);
    }
}
