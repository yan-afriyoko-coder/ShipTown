<?php

use App\Models\Picklist;
use Illuminate\Database\Seeder;

class PicklistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Picklist::class, 100)
            ->create();
    }
}
