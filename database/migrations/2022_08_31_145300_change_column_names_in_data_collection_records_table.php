<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeColumnNamesInDataCollectionRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('data_collection_records', function (Blueprint $table) {
            $table->dropColumn('user_id');
            $table->dropColumn('quantity_required');
        });

        Schema::table('data_collection_records', function (Blueprint $table) {
            $table->renameColumn('quantity_expected', 'quantity_requested');
            $table->renameColumn('quantity_collected', 'quantity_scanned');
        });

        Schema::table('data_collection_records', function (Blueprint $table) {
            $table->decimal('quantity_to_scan', 20)->after('quantity_scanned')
                ->storedAs('CASE WHEN quantity_requested < quantity_scanned THEN 0 ' .
                    'ELSE quantity_requested - quantity_scanned END')
                ->comment('CASE WHEN quantity_requested < quantity_scanned THEN 0 ' .
                    'ELSE quantity_requested - quantity_scanned END');
        });
    }
}
