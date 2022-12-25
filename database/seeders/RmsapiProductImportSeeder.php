<?php

namespace Database\Seeders;

use App\Modules\Rmsapi\src\Models\RmsapiProductImport;
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
        RmsapiProductImport::factory()->create();
    }
}
