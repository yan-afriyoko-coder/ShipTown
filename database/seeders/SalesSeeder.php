<?php

namespace Database\Seeders;

use App\Models\InventoryMovement;
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
        InventoryMovement::factory()->count(600)->create([
            'type' => InventoryMovement::TYPE_SALE,
        ]);
    }
}
