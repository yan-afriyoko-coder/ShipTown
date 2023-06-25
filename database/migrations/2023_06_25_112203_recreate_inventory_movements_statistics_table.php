<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::drop('inventory_movements_statistics');

        Schema::create('inventory_movements_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_id')->unique();
            $table->foreignId('product_id')->index();
            $table->foreignId('warehouse_id')->index();
            $table->string('warehouse_code', 5)->index();
            $table->foreignId('last_inventory_movement_id')->index()->nullable();
            $table->decimal('quantity_sold_last_7_days', 10)->index()->nullable();
            $table->decimal('quantity_sold_last_14_days', 10)->index()->nullable();
            $table->decimal('quantity_sold_last_28_days', 10)->index()->nullable();
            $table->decimal('quantity_sold_this_week', 10)->index()->nullable();
            $table->decimal('quantity_sold_last_week', 10)->index()->nullable();
            $table->decimal('quantity_sold_2weeks_ago', 10)->index()->nullable();
            $table->decimal('quantity_sold_3weeks_ago', 10)->index()->nullable();
            $table->decimal('quantity_sold_4weeks_ago', 10)->index()->nullable();
            $table->timestamps();
        });

        DB::statement("
            INSERT INTO inventory_movements_statistics (
                inventory_id, product_id, warehouse_id, warehouse_code, updated_at, created_at
            )
            SELECT
                id as inventory_id,
                product_id,
                warehouse_id,
                warehouse_code,
                now() as updated_at,
                now() as created_at
            FROM `inventory`
        ");
    }
};
