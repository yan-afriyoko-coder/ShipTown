<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('modules_rmsapi_products_imports', function (Blueprint $table) {
            $table->unsignedBigInteger('inventory_id')->nullable()->after('warehouse_code');
            $table->decimal('reorder_point', 20)->nullable()->after('inventory_id');
            $table->decimal('restock_level', 20)->nullable()->after('reorder_point');

            $table->decimal('price', 20)->nullable()->after('restock_level');
            $table->decimal('cost', 20)->nullable()->after('price');
            $table->decimal('sale_price', 20)->nullable()->after('cost');
            $table->timestamp('sale_price_start_date')->nullable()->after('sale_price');
            $table->timestamp('sale_price_end_date')->nullable()->after('sale_price_start_date');
            $table->boolean('is_web_item')->nullable()->after('quantity_on_order');
            $table->string('department_name')->nullable()->after('is_web_item');
            $table->string('category_name')->nullable()->after('department_name');
            $table->string('sub_description_1')->nullable()->after('category_name');
            $table->string('sub_description_2')->nullable()->after('sub_description_1');
            $table->string('sub_description_3')->nullable()->after('sub_description_2');
            $table->string('supplier_name')->nullable()->after('sub_description_3');

            $table->foreign('inventory_id')
                ->references('id')
                ->on('inventory')
                ->cascadeOnDelete();
        });
    }
};
