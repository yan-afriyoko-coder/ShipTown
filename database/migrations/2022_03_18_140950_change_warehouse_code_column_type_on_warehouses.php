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
        try {
            Schema::table('warehouses', function (Blueprint $table) {
                $table->dropUnique('warehouses_code_unique');
            });
        } catch (Exception $exception) {
            //
        };

        Schema::table('warehouses', function (Blueprint $table) {
            $table->string('code', 5)->nullable(false)->unique()->change();
        });

        Schema::table('inventory', function (Blueprint $table) {
            $table->string('warehouse_code', 5)->nullable(false)->change();
        });

        Schema::table('products_prices', function (Blueprint $table) {
            $table->string('warehouse_code', 5)->nullable(false)->change();
        });

        Schema::table('modules_api2cart_connections', function (Blueprint $table) {
            $table->string('inventory_location_id', 5)->nullable(true)->change();
            $table->string('pricing_location_id', 5)->nullable(true)->change();
        });
    }
}
