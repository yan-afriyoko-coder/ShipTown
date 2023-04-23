<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePicksOrderProductsTable extends Migration
{
    public function up(): void
    {
        Schema::create('picks_orders_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pick_id')->constrained('picks')->cascadeOnDelete();
            $table->foreignId('order_product_id')->nullable()->constrained('orders_products')->nullOnDelete();
            $table->decimal('quantity_picked', 10, 2)->default(0);
            $table->decimal('quantity_skipped_picking', 10, 2)->default(0);
            $table->softDeletes();
            $table->timestamps();
        });
    }
}
