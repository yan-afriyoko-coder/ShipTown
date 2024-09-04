<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class FulfilmentCenterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /** @var Warehouse $fulfilmentCenter */
        $fulfilmentCenter = Warehouse::firstOrCreate(['code' => '99'], ['name' => 'Warehouse']);

        $fulfilmentCenter->attachTag('fulfilment');
    }
}
