<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_movements_new', function (Blueprint $table) {
            $table->unsignedBigInteger('id'); // index and autoincrement are created at the bottom of this migration
            $table->foreignId('inventory_id');
            $table->foreignId('product_id');
            $table->string('warehouse_code', 5);
            $table->foreignId('user_id')->nullable();
            $table->string('type');
            $table->decimal('quantity_before', 20);
            $table->decimal('quantity_delta', 20);
            $table->decimal('quantity_after', 20);
            $table->unsignedBigInteger('previous_movement_id')->nullable()->unique();
            $table->unsignedBigInteger('next_movement_id')->nullable()->unique();
            $table->string('custom_unique_reference_id')->nullable()->unique();
            $table->string('description', 50);
            $table->timestamps();
            $table->boolean('is_first_movement')->nullable();
            $table->foreignId('warehouse_id');

            $table->index('type');
            $table->index('is_first_movement');

            $table->foreign('previous_movement_id')
                ->references('id')
                ->on('inventory_movements')
                ->onDelete('SET NULL');

            $table->foreign('next_movement_id')
                ->references('id')
                ->on('inventory_movements')
                ->onDelete('SET NULL');

            $table->foreign('inventory_id')
                ->references('id')
                ->on('inventory')
                ->restrictOnDelete();

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->restrictOnDelete();

            $table->foreign('warehouse_code')
                ->references('code')
                ->on('warehouses')
                ->restrictOnDelete();

            $table->foreign('warehouse_id')
                ->references('id')
                ->on('warehouses')
                ->restrictOnDelete();

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->restrictOnDelete();
        });

        DB::statement('ALTER TABLE inventory_movements_new ADD PRIMARY KEY id (id DESC)');
        DB::statement('ALTER TABLE inventory_movements_new CHANGE id id bigint unsigned NOT NULL AUTO_INCREMENT FIRST;');
    }
};
