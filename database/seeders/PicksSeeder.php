<?php

namespace Database\Seeders;

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
        Pick::factory()->count(rand(1000, 2000))->create();
    }
}
