<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('data_collection_records', function (Blueprint $table) {
            $table->boolean('is_scanned')->after('quantity_to_scan')
                ->storedAs('quantity_to_scan <= 0')
                ->comment('quantity_to_scan <= 0');
        });

        Schema::table('data_collection_records', function (Blueprint $table) {
            $table->index('is_scanned');
        });
    }
};
