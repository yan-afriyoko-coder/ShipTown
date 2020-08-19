<?php

use App\Models\Order;
use App\Models\OrderProduct;
use App\Services\OrderService;
use Illuminate\Database\Seeder;

class SingleLineOrderPicklistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $orders = factory(Order::class,5)
            ->create(['status_code' => 'processing'])
            ->each(function (Order $order) {
                $orderProducts = factory(OrderProduct::class, 1)->make();

                $order->orderProducts()->saveMany($orderProducts);
            });

        foreach ($orders as $order) {
            OrderService::addToPicklist($order);
        }
    }
}
