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
            $table->dropUnique('warehouses_code_unique');
        });

        Schema::table('warehouses', function (Blueprint $table) {
            $table->string('code', 5)->nullable(false)->unique()->change();
        });

        Schema::table('inventory', function (Blueprint $table) {
            $table->string('warehouse_code', 5)->nullable(false)->change();
//
//            $table->foreign('warehouse_code')
//                ->on('warehouses')
//                ->references('code')
//                ->onDelete('CASCADE');
        });

        Schema::table('products_prices', function (Blueprint $table) {
            $table->string('warehouse_code', 5)->nullable(false)->change();
//
//            $table->foreign('warehouse_code')
//                ->on('warehouses')
//                ->references('code')
//                ->onDelete('CASCADE');
        });

        Schema::table('modules_api2cart_connections', function (Blueprint $table) {
            $table->string('location_id', 5)->nullable(true)->change();
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
