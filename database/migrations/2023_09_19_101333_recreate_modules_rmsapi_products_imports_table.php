<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('modules_rmsapi_products_imports');

        Schema::create('modules_rmsapi_products_imports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('connection_id');
            $table->string('warehouse_code')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('inventory_id')->nullable();
            $table->unsignedBigInteger('warehouse_id')->nullable();
            $table->unsignedBigInteger('rms_product_id')->nullable();
            $table->string('sku')->nullable();
            $table->boolean('is_web_item')->nullable();
            $table->decimal('quantity_on_hand', 20)->nullable();
            $table->decimal('quantity_committed', 20)->nullable();
            $table->decimal('quantity_available', 20)->nullable();
            $table->decimal('quantity_on_order', 20)->nullable();
            $table->decimal('reorder_point', 20)->nullable();
            $table->decimal('restock_level', 20)->nullable();
            $table->decimal('price', 20)->nullable();
            $table->decimal('cost', 20)->nullable();
            $table->decimal('sale_price', 20)->nullable();
            $table->timestamp('sale_price_start_date')->nullable();
            $table->timestamp('sale_price_end_date')->nullable();
            $table->string('department_name')->nullable();
            $table->string('category_name')->nullable();
            $table->string('sub_description_1')->nullable();
            $table->string('sub_description_2')->nullable();
            $table->string('sub_description_3')->nullable();
            $table->string('supplier_name')->nullable();
            $table->timestamp('reserved_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->uuid('batch_uuid')->nullable();
            $table->json('raw_import')->nullable();
            $table->timestamps();

            $table->index('connection_id');
            $table->index('rms_product_id');
            $table->index('reserved_at');
            $table->index('processed_at');
            $table->index('is_web_item');

            $table->foreign('inventory_id')
                ->references('id')
                ->on('inventory')
                ->cascadeOnDelete();

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->cascadeOnDelete();

            $table->foreign('connection_id')
                ->references('id')
                ->on('modules_rmsapi_connections')
                ->cascadeOnDelete();

            $table->foreign('warehouse_id')
                ->references('id')
                ->on('warehouses')
                ->cascadeOnDelete();

            $table->foreign('warehouse_code')
                ->references('code')
                ->on('warehouses')
                ->cascadeOnDelete();
        });
    }
};
