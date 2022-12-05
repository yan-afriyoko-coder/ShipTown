<?php

use App\Models\Order;
use Illuminate\Database\Seeder;

class UnpaidOrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Order::factory()->count(rand(1, 10))
            ->with('orderProducts', 1)
            ->create(['status_code' => 'processing']);

        Order::factory()->count(rand(1, 10))
            ->with('orderProducts', 2)
            ->create(['status_code' => 'processing']);

        Order::factory()->count(rand(1, 10))
            ->with('orderProducts', 4)
            ->create(['status_code' => 'processing']);
    }
}
