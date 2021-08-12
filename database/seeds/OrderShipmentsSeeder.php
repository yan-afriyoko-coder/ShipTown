<?php

use App\Models\OrderShipment;
use Illuminate\Database\Seeder;

class OrderShipmentsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(OrderShipment::class, rand(100, 200))->create();
    }
}
