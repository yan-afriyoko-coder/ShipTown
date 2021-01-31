<?php

use App\Models\Order;
use App\Models\Pick;
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
        factory(Pick::class, rand(1000, 2000))->create();
    }
}
