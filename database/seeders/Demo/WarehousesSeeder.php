<?php

namespace Database\Seeders\Demo;

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
        Warehouse::firstOrCreate(['code' => 'WHS'], ['name' => 'Warehouse'])->attachTag('fulfilment');
        Warehouse::firstOrCreate(['code' => '999'], ['name' => 'Website Orders']);
        Warehouse::firstOrCreate(['code' => 'DUB'], ['name' => 'Dublin']);
        Warehouse::firstOrCreate(['code' => 'CRK'], ['name' => 'Cork']);
        Warehouse::firstOrCreate(['code' => 'GAL'], ['name' => 'Galway']);
    }
}
