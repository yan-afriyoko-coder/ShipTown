<?php

use App\Models\Order;
use App\Models\OrderAddress;
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
        factory(Order::class, 100)
            ->with('orderProducts', 1)
            ->create(['status_code' => 'processing']);
//
        factory(Order::class, 100)
            ->with('orderProducts', 2)
            ->create(['status_code' => 'processing']);

        factory(Order::class, 100)
            ->with('orderProducts', 3)
            ->create(['status_code' => 'processing']);

        factory(Order::class, 100)
            ->with('orderProducts', 4)
            ->create(['status_code' => 'processing']);

        Order::all()->each(function (Order $order) {
            $order->total_paid = $order->total;
            $order->save();
        });
    }
}
