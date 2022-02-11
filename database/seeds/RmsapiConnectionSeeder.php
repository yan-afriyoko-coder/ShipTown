<?php

use App\Modules\Rmsapi\src\Models\RmsapiConnection;
use Illuminate\Database\Seeder;

class RmsapiConnectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(RmsapiConnection::class)->create();
    }
}
