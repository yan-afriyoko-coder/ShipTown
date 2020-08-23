<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProductsPriceSalePriceChangeMaxFloatSize extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table("products", function ($table) {
            $table->decimal('price', 10, 2)->default(0)->change();
            $table->decimal('sale_price', 10, 2)->default(0)->change();
            $table->decimal('quantity', 10, 2)->default(0)->change();
            $table->decimal('quantity_reserved', 10, 2)->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table("products", function ($table) {
            $table->decimal('price', 8, 2)->default(0)->change();
            $table->decimal('sale_price', 8, 2)->default(0)->change();
            $table->decimal('quantity')->default(0)->change();
            $table->decimal('quantity_reserved')->default(0)->change();
        });
    }
}
