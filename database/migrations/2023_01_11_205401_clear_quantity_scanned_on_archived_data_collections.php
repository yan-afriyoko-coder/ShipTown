<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ClearQuantityScannedOnArchivedDataCollections extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('
            UPDATE data_collection_records

            RIGHT JOIN data_collections
                on data_collections.id = data_collection_records.data_collection_id
                and data_collections.deleted_at IS NOT NULL

            SET quantity_scanned = 0

            WHERE `quantity_scanned` != 0
        ');
    }
}
