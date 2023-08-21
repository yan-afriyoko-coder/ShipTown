<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('data_collections', function (Blueprint $table) {
            $table->unsignedBigInteger('destination_collection_id')->nullable()->after('warehouse_id');

            $table->foreign('destination_collection_id')
                ->references('id')
                ->on('data_collections')
                ->restrictOnDelete();
        });
    }
};
