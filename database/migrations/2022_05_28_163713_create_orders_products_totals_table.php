<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersProductsTotalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders_products_totals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id');
            $table->integer('count');
            $table->decimal('quantity_ordered', 20)->default(0);
            $table->decimal('quantity_split', 20)->default(0);
            $table->decimal('quantity_picked', 20)->default(0);
            $table->decimal('quantity_skipped_picking', 20)->default(0);
            $table->decimal('quantity_not_picked', 20)->default(0);
            $table->decimal('quantity_shipped', 20)->default(0);
            $table->decimal('quantity_to_pick', 20)
                ->storedAs('quantity_ordered - quantity_split - quantity_picked - quantity_skipped_picking')
                ->comment('quantity_ordered - quantity_split - quantity_picked - quantity_skipped_picking');
            $table->decimal('quantity_to_ship', 20)
                ->storedAs('quantity_ordered - quantity_split - quantity_shipped')
                ->comment('quantity_ordered - quantity_split - quantity_shipped');
            $table->timestamps();
        });
    }
}
