<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQuantityExpectedColumnToDataCollectionRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('data_collection_records', function (Blueprint $table) {
            $table->renameColumn('quantity', 'quantity_collected');
        });

        Schema::table('data_collection_records', function (Blueprint $table) {
            $table->decimal('quantity_expected', 20)->nullable()->after('product_id');
            $table->decimal('quantity_collected', 20)->default(0)->change();
            $table->decimal('quantity_required', 20)->after('quantity_collected')
                ->storedAs('CASE WHEN quantity_expected < quantity_collected THEN 0 ' .
                    'ELSE quantity_expected - quantity_collected END')
                ->comment('CASE WHEN quantity_expected < quantity_collected THEN 0 ' .
                    'ELSE quantity_expected - quantity_collected END');
        });
    }
}
