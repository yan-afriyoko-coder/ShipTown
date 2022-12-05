<?php

namespace Database\Seeders;

use App\Modules\Api2cart\src\Models\Api2cartOrderImports;
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
        Api2cartOrderImports::factory()->create();
    }
}
