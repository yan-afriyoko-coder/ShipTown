<?php

use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Database\Seeder;

class PacklistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $orderList = Order::query()->inRandomOrder()->take(5)->get();

        foreach ($orderList as $order) {
            OrderService::addToPacklist($order);

            $order->update(['is_picked' => true]);
        }
    }
}
