<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        try {
            Schema::table('inventory_movements', function (Blueprint $table) {
                $table->dropForeign(['warehouse_code']);
            });
        } catch (Throwable $th) {
            //throw $th;
        }

        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->string('warehouse_code', 5)->nullable(false);
        });

        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->foreign('warehouse_code')
                ->references('code')
                ->on('warehouses')
                ->restrictOnDelete();
        });
    }
};
