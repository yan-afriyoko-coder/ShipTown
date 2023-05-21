<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products_prices', function (Blueprint $table) {
            $table->decimal('price', 20)->default(0)->change();
            $table->decimal('sale_price', 20)->default(0)->change();
        });
    }
};
