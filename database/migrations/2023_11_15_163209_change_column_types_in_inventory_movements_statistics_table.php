<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_movements_statistics', function (Blueprint $table) {
            $table->decimal('last7days_quantity_delta', 13, 2)->default(0)->change();
            $table->decimal('last14days_quantity_delta', 13, 2)->default(0)->change();
            $table->decimal('last28days_quantity_delta', 13, 2)->default(0)->change();
        });
    }
};
