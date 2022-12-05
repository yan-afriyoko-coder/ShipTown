<?php

namespace Database\Seeders;

use App\Modules\PrintNode\src\Models\Client;
use App\User;
use Illuminate\Database\Seeder;

class PrintNodeClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       if (env('TEST_PRINTNODE_KEY')) {
           $client = Client::firstOrNew();
           $client->api_key = env('TEST_PRINTNODE_KEY');
           $client->save();
       }

       if (env('TEST_PRINTNODE_PRINTER_ID')) {
           User::query()->update(['printer_id' => env('TEST_PRINTNODE_PRINTER_ID')]);
       }
    }
}
