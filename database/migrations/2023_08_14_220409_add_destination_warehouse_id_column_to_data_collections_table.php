<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('data_collections', function (Blueprint $table) {
            $table->unsignedBigInteger('destination_warehouse_id')->nullable()->after('warehouse_id');

            $table->foreign('destination_warehouse_id')
                ->references('id')
                ->on('warehouses');
        });
    }
};
