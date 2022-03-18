<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWarehouseCodeColumnToProductsPrices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products_prices', function (Blueprint $table) {
            $table->string('warehouse_code')->after('location_id')->default('');
        });

        \Illuminate\Support\Facades\DB::statement('UPDATE products_prices SET warehouse_code = location_id');
    }
}
