<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpadateIncorrectLocationIdOnProductsPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::statement('UPDATE products_prices SET location_id = warehouse_code
WHERE `warehouse_code` != location_id');
        \Illuminate\Support\Facades\DB::statement('UPDATE inventory SET location_id = warehouse_code
WHERE `warehouse_code` != location_id');
    }
}
