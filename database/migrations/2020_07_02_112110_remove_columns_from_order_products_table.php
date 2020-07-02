<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveColumnsFromOrderProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_products', function (Blueprint $table) {
            $table->dropColumn('order_product_id');
            $table->dropColumn('price_inc_tax');
            $table->dropColumn('discount_amount');
            $table->dropColumn('total_price');
            $table->dropColumn('tax_percent');
            $table->dropColumn('tax_value');
            $table->dropColumn('tax_value_after_discount');
            $table->dropColumn('variant_id');
            $table->dropColumn('weight_unit');
            $table->dropColumn('weight');
            $table->dropColumn('parent_order_product_id');
            $table->dropColumn('additional_fields');
            $table->dropColumn('custom_fields');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_products', function (Blueprint $table) {
            $table->string('order_product_id')->nullable();
            $table->decimal('price_inc_tax', 15, 2)->default('0.00')->nullable();
            $table->decimal('discount_amount', 15, 2)->default('0.00')->nullable();
            $table->decimal('total_price', 15, 2)->default('0.00')->nullable();
            $table->unsignedInteger('tax_percent')->default(0)->nullable();
            $table->decimal('tax_value', 15, 2)->default('0.00')->nullable();
            $table->decimal('tax_value_after_discount', 15, 2)->default('0.00')->nullable();
            $table->string('variant_id')->nullable();
            $table->string('weight_unit')->nullable();
            $table->unsignedInteger('weight')->default(0)->nullable();
            $table->string('parent_order_product_id')->nullable();
            $table->json('additional_fields')->nullable();
            $table->json('custom_fields')->nullable();
        });
    }
}
