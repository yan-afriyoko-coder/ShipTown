<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders_products', function (Blueprint $table) {
            $table->boolean('is_shipped')->after('name_ordered')
                ->storedAs('quantity_ordered - quantity_split - quantity_shipped <= 0')
                ->comment('quantity_ordered - quantity_split - quantity_shipped <= 0');
        });

        Schema::table('orders_products', function (Blueprint $table) {
            $table->index('is_shipped');
        });
    }
};
