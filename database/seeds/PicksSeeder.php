<?php

use App\Models\Order;
use Illuminate\Database\Seeder;

class PicksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $orders = factory(Order::class, rand(2,15))
            ->with('orderProducts', rand(2,5))
            ->create(['status_code' => 'processing']);

        foreach ($orders as $order) {
            $order->update(['status_code' => 'picking']);
        }
    }
}
