<?php

namespace Database\Seeders\Demo;

use App\Models\NavigationMenu;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Models\ProductAlias;
use App\Modules\Automations\src\Actions\Order\SetStatusCodeAction;
use App\Modules\Automations\src\Conditions\Order\IsFullyPackedCondition;
use App\Modules\Automations\src\Conditions\Order\StatusCodeEqualsCondition;
use App\Modules\Automations\src\Models\Automation;
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
        $order = Order::factory()
            ->count(1)
            ->create(['order_number' => 'test order - packsheet', 'status_code' => 'paid']);

        $product = Product::query()->where(['sku' => '45'])->first() ?? Product::factory()->create(['sku' => '45']);

        OrderProduct::factory()
            ->count(1)
            ->create([
                'order_id' => $order->first()->getKey(),
                'quantity_ordered' => 100,
                'product_id' => $product->getKey(),
            ]);
    }
}
