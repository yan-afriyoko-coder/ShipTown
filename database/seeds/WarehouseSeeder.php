<?php

use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Warehouse::class)->create(['code' => 'DUB', 'name' => 'Dublin']);
        factory(Warehouse::class)->create(['code' => 'CRK', 'name' => 'Cork']);
        factory(Warehouse::class)->create(['code' => 'GAL', 'name' => 'Galway']);
    }
}
