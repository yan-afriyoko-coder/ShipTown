<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        do {
            Schema::dropIfExists('tempTable');

            DB::statement('
                CREATE TEMPORARY TABLE tempTable AS
                SELECT id as data_collection_record_id
                FROM `data_collection_records`
                WHERE `inventory_id` IS NULL
                LIMIT 10000;
            ');

            $recordsUpdated = DB::update('
                UPDATE data_collection_records

                INNER JOIN tempTable
                  ON tempTable.data_collection_record_id = data_collection_records.id

                LEFT JOIN data_collections
                  ON data_collections.id = data_collection_records.data_collection_id

                LEFT JOIN inventory
                  ON inventory.product_id = data_collection_records.product_id
                  AND inventory.warehouse_id = data_collections.warehouse_id

                SET data_collection_records.inventory_id = inventory.id;
            ');
        } while ($recordsUpdated > 0);
    }
};
