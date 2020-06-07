<?php

use App\Models\CompanyConfiguration;
use Illuminate\Database\Seeder;

class CompanyConfigurationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(CompanyConfiguration::class)->create();
    }
}
