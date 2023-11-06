<?php

namespace Database\Seeders\Demo;

use App\Models\Order;
use App\Models\OrderComment;
use App\Models\OrderProduct;
use App\Services\OrderService;
use Illuminate\Database\Seeder;

class OrderProductNotExistsSeeder extends Seeder
{
    public function run()
    {
        $order = Order::factory()->create(['order_number' => 'T100001', 'status_code' => 'paid']);

        OrderComment::create([
            'order_id' => $order->getKey(),
            'comment' => 'Product with SKU ordered does not exist in the system. This simulates scenario when product exists in remote system (Magento, Shopify etc) but no in our system.'
        ]);

        OrderComment::create([
            'order_id' => $order->getKey(),
            'comment' => 'Test order'
        ]);

        OrderProduct::factory()->create([
            'order_id' => $order->getKey(),
            'sku_ordered' => '123123123123123',
            'quantity_ordered' => 1,
            'product_id' => null,
        ]);
    }
}
