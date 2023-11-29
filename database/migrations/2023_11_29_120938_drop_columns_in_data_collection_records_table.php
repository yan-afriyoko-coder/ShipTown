<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('data_collection_records', 'is_scanned')) {
            Schema::table('data_collection_records', function (Blueprint $table) {
                $table->dropIndex(['is_scanned']);
            });

            Schema::dropColumns('data_collection_records', ['is_scanned']);
        }

        if (Schema::hasColumn('data_collection_records', 'quantity_to_scan')) {
            Schema::dropColumns('data_collection_records', ['quantity_to_scan']);
        }
    }
};
