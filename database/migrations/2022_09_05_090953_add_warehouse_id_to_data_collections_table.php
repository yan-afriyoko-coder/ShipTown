<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWarehouseIdToDataCollectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('data_collections', function (Blueprint $table) {
            $table->foreignId('warehouse_id')->after('id')
                ->references('id')
                ->on('warehouses')
                ->onDelete('cascade');
        });
    }
}
