<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('order_id')->nullable();
            $table->string('product_id');
            $table->string('order_product_id')->nullable();
            $table->string('model');
            $table->string('name');
            $table->decimal('price', 15, 2)->default('0.00')->nullable();
            $table->decimal('price_inc_tax', 15, 2)->default('0.00')->nullable();
            $table->unsignedInteger('quantity')->default(0);
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
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_products');
    }
}
