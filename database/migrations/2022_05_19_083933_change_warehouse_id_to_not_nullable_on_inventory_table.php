<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeWarehouseIdToNotNullableOnInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->dropForeign(['warehouse_id']);
        });

        Schema::table('inventory', function (Blueprint $table) {
            $table->foreignId('warehouse_id')->nullable(false)->change();
        });

        Schema::table('inventory', function (Blueprint $table) {
            $table->foreign('warehouse_id')
                ->on('warehouses')
                ->references('id')
                ->onDelete('cascade');
        });
    }
}
