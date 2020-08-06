<?php

use App\Models\Order;
use App\Models\OrderProduct;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class OrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Order::class,2)
            ->create()
            ->each(function (Order $order) {
                $orderProducts = factory(OrderProduct::class, rand(1,3))->make();

                $order->orderProducts()->saveMany($orderProducts);
            });

        factory(Order::class,2)
            ->create()
            ->each(function (Order $order) {
                $orderProducts = factory(OrderProduct::class, 1)->make();

                $order->orderProducts()->saveMany($orderProducts);
            });

        // we fabricate few orders with SKU not present in database
        factory(Order::class,1)
            ->create()
            ->each(function (Order $order) {
                $orderProducts = collect(factory(OrderProduct::class,1)->make())
                    ->map(function ($orderProduct) {

                        $suffix = Arr::random(['-blue', '-red', '-green', '-xl', '-small-orange']);

                        $orderProduct['product_id'] = null;
                        $orderProduct['sku_ordered'] = $orderProduct['sku_ordered'] . $suffix;
                        $orderProduct['name_ordered'] = $orderProduct['name_ordered'] . $suffix;

                        return $orderProduct;
                    });

                $order->orderProducts()->saveMany($orderProducts);
            });

    }
}
