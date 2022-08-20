<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryTotalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventory_totals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->unique();
            $table->decimal('quantity', 20)->default(0);
            $table->decimal('quantity_reserved', 20)->default(0);
            $table->decimal('quantity_incoming', 20)->default(0);
            $table->decimal('quantity_available', 20)
                ->storedAs('quantity - quantity_reserved')
                ->comment('quantity - quantity_reserved');
            $table->softDeletes();
            $table->timestamps();

            $table->index('quantity');
            $table->index('quantity_reserved');
            $table->index('quantity_incoming');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventory_totals');
    }
}
