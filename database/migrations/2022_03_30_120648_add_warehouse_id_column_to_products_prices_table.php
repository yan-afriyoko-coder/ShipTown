<?php

use App\Models\ProductPrice;
use App\Models\Warehouse;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWarehouseIdColumnToProductsPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products_prices', function (Blueprint $table) {
            $table->foreignId('warehouse_id')->nullable(true)->after('product_id');
        });

        Warehouse::all()->each(function (Warehouse $warehouse) {
            ProductPrice::whereWarehouseCode($warehouse->code)->update(['warehouse_id' => $warehouse->id]);
        });

        Schema::table('products_prices', function (Blueprint $table) {
            $table->foreignId('warehouse_id')->nullable(false)->change();

            $table->foreign('warehouse_id')
                ->references('id')
                ->on('warehouses')
                ->onDelete('CASCADE');
        });
    }
}
