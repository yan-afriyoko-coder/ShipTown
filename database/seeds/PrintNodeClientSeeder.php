<?php

use App\Modules\PrintNode\src\Models\Client;
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
       $test_printnode_key = env('TEST_PRINTNODE_KEY');

       if ($test_printnode_key) {
           $client = Client::firstOrNew();
           $client->api_key = $test_printnode_key;
           $client->save();
       }
    }
}
