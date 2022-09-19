<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInventoryIdColumnToDataCollectionRecordsTable extends Migration
{
    public function up()
    {
        Schema::table('data_collection_records', function (Blueprint $table) {
            $table->foreignId('inventory_id')->nullable()
                ->after('data_collection_id')
                ->references('id')
                ->on('inventory');
        });
    }
}
