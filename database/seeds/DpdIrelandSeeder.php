<?php

use Illuminate\Database\Seeder;

class DpdIrelandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(env('TEST_DPD_USER')) {
            factory(\App\Modules\DpdIreland\src\Models\DpdIreland::class)->create();
        }
    }
}
