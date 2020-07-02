<?php

use App\Models\Order;
use App\Models\OrderProduct;
use Illuminate\Database\Seeder;

class OrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Order::class,50)
            ->create()->each(function ($order) {
                $orderProducts = factory(OrderProduct::class, rand(1,20))->make();

                $order->orderProducts()->saveMany($orderProducts);
            });
    }
}
