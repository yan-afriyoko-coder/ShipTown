<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddTotalInTotalOutColumnsToDataCollectionRecordsTable extends Migration
{
    public function up()
    {
        Schema::table('data_collection_records', function (Blueprint $table) {
            $table->double('total_transferred_in', 10)->default(0)->after('product_id');
            $table->double('total_transferred_out', 10)->default(0)->after('total_transferred_in');
        });

        Schema::table('data_collection_records', function (Blueprint $table) {
            $table->dropColumn('quantity_to_scan');
        });

        Schema::table('data_collection_records', function (Blueprint $table) {
            $table->decimal('quantity_to_scan', 20)->after('quantity_scanned')
                ->storedAs('CASE WHEN quantity_requested - total_transferred_out - total_transferred_in - quantity_scanned < quantity_scanned THEN 0 ' .
                    'ELSE quantity_requested - total_transferred_out - total_transferred_in - quantity_scanned END')
                ->comment('CASE WHEN quantity_requested - total_transferred_out - total_transferred_in - quantity_scanned < quantity_scanned THEN 0 ' .
                    'ELSE quantity_requested - total_transferred_out - total_transferred_in - quantity_scanned - quantity_scanned END');
        });
    }
}
