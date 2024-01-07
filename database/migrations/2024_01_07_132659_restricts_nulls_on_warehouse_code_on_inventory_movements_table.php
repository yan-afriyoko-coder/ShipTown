<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->dropForeign(['warehouse_code']);
        });

        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->string('warehouse_code')->nullable(false)->change();
        });

        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->foreign('warehouse_code')
                ->references('code')
                ->on('warehouses')
                ->restrictOnDelete();
        });
    }
};
