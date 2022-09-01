<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDataCollectionIdColumnToDataCollectionsRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\DataCollectionRecord::query()->forceDelete();
        Schema::table('data_collection_records', function (Blueprint $table) {
            $table->foreignId('data_collection_id')->after('id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('data_collections_records', function (Blueprint $table) {
            //
        });
    }
}
