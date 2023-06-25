<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_movements_statistics', function (Blueprint $table) {
            $table->foreign('inventory_id')
                ->references('id')
                ->on('inventory')
                ->onDelete('cascade');

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->onDelete('cascade');

            $table->foreign('warehouse_id')
                ->references('id')
                ->on('warehouses')
                ->onDelete('cascade');

            $table->foreign('warehouse_code')
                ->references('code')
                ->on('warehouses')
                ->onDelete('cascade');
        });
    }
};
