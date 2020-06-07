<?php

use App\Models\Api2cartOrderImports;
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
        factory(Api2cartOrderImports::class)->create();
    }
}
