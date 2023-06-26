<?php

namespace Database\Seeders;

use App\Models\Inventory;
use App\Services\InventoryService;
use Illuminate\Database\Seeder;

class SalesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i=0; $i < 600; $i++) {
            InventoryService::sellProduct(Inventory::query()->inRandomOrder()->first(), rand(-100, -1), '');
        }
    }
}
