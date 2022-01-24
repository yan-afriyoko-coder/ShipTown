<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('products')) {
            return;
        }

        Schema::create('products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('sku', 50)->unique();
            $table->string('name', 100)->default('');
            $table->decimal('price', 10, 2)->default(0);
            $table->decimal('sale_price', 10, 2)->default(0);
            $table->date('sale_price_start_date')->default('1899-01-01');
            $table->date('sale_price_end_date')->default('1899-01-01');
            $table->string('commodity_code')->default('');
            $table->decimal('quantity', 10, 2)->default(0);
            $table->decimal('quantity_reserved', 10, 2)->default(0);
            $table->decimal('quantity_available', 10, 2)->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }
}
