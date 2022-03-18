<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeWarehouseCodeColumnTypeOnWarehouses extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('warehouses', function (Blueprint $table) {
            $table->string('code', 5)->nullable(false)->change();
        });

        Schema::table('inventory', function (Blueprint $table) {
            $table->string('warehouse_code', 5)->nullable(false)->change();
        });

        Schema::table('products_prices', function (Blueprint $table) {
            $table->string('warehouse_code', 5)->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
