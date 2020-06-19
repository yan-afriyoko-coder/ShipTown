<?php

use Illuminate\Database\Seeder;

class RmsapiProductImportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(\App\Models\RmsapiImportedProduct::class)->create();
    }
}
