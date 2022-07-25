<?php

use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehousesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Warehouse::firstOrCreate(['code' => '99'], ['name'  => 'Warehouse']);
        Warehouse::firstOrCreate(['code' => 'DUB'], ['name' => 'Dublin']);
        Warehouse::firstOrCreate(['code' => 'CRK'], ['name' => 'Cork']);
        Warehouse::firstOrCreate(['code' => 'GAL'], ['name' => 'Galway']);
        Warehouse::firstOrCreate(['code' => '999'], ['name' => 'Web']);
    }
}
