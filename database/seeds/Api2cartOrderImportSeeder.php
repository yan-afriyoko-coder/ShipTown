<?php

use App\Models\Api2CartOrderImports;
use Illuminate\Database\Seeder;

class Api2CartOrderImportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Api2CartOrderImports::class)->create();
    }
}
