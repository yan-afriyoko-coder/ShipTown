<?php

use App\Models\Order;
use App\Models\Picklist;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class PicklistSeeder extends Seeder
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
            OrderService::addToPicklist($order);
        }
    }
}
