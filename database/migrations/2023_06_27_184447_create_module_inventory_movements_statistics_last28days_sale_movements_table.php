<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modules_inventory_movements_statistics_last28days_sale_movements', function (Blueprint $table) {
            $table->foreignId('inventory_movement_id')->unique('inventory_movement_id_index');
            $table->foreignId('inventory_id')->index('inventory_id_index');
            $table->dateTime('sold_at')->index('sold_at_index');
            $table->decimal('quantity_sold', 20, 3);
        });
    }
};
