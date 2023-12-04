<?php

namespace Database\Seeders\Demo;

use App\Models\Order;
use App\Models\OrderComment;
use App\Models\OrderProduct;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestOrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->create_test_unpaid_order();

        $this->create_order_with_sku_not_in_our_database();

        $this->create_test_order_for_packing();
    }

    protected function create_order_with_sku_not_in_our_database(): void
    {
        $order = Order::query()->create(['order_number' => 'T100001', 'order_placed_at' => now()]);

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

        Order::query()->where(['id' => $order->getKey()])->update(['total_paid' => DB::raw('total_order')]);
    }

    protected function create_test_order_for_packing(): void
    {
        $product1 = Product::query()->firstOrCreate(['sku' => '45'], ['name' => 'Test Product - 45']);
        $product2 = Product::query()->firstOrCreate(['sku' => '44'], ['name' => 'Test Product - 44']);

        $order = Order::query()->create(['order_number' => 'T100002 - Packsheet', 'status_code' => 'paid', 'order_placed_at' => now()]);

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

        Order::query()->where(['id' => $order->getKey()])->update(['total_paid' => DB::raw('total_order')]);
    }

    protected function create_test_unpaid_order(): void
    {
        $product1 = Product::query()->firstOrCreate(['sku' => '45'], ['name' => 'Test Product - 45']);
        $product2 = Product::query()->firstOrCreate(['sku' => '44'], ['name' => 'Test Product - 44']);

        $order = Order::query()->create(['order_number' => 'T100002 - Unpaid order', 'order_placed_at' => now(), 'total_paid' => 0]);

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
