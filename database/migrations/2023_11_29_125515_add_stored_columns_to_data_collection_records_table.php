<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('data_collection_records', function (Blueprint $table) {
            $table->decimal('quantity_to_scan', 20)->after('quantity_scanned')
                ->storedAs('GREATEST(0, IFNULL(quantity_requested, 0) - IFNULL(total_transferred_out, 0) - IFNULL(total_transferred_in, 0) - IFNULL(quantity_scanned, 0))')
                ->comment('GREATEST(0, IFNULL(quantity_requested, 0) - IFNULL(total_transferred_out, 0) - IFNULL(total_transferred_in, 0) - IFNULL(quantity_scanned, 0))');
        });

        Schema::table('data_collection_records', function (Blueprint $table) {
            $table->boolean('is_requested')->after('quantity_to_scan')
                ->storedAs('IFNULL(data_collection_records.quantity_requested, 0) = 0')
                ->comment('IFNULL(data_collection_records.quantity_requested, 0) = 0');
            $table->boolean('is_fully_scanned')->after('is_requested')
                ->storedAs('quantity_to_scan <= 0')
                ->comment('quantity_to_scan <= 0');
            $table->boolean('is_over_scanned')->after('is_fully_scanned')
                ->storedAs('IFNULL(data_collection_records.quantity_scanned, 0) > IFNULL(data_collection_records.quantity_requested, 0)')
                ->comment('IFNULL(data_collection_records.quantity_scanned, 0) > IFNULL(data_collection_records.quantity_requested, 0)');
        });

        Schema::table('data_collection_records', function (Blueprint $table) {
            $table->index('is_requested');
            $table->index('is_fully_scanned');
            $table->index('is_over_scanned');
        });
    }
};
