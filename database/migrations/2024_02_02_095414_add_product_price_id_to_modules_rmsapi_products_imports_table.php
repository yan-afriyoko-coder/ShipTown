<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('modules_rmsapi_products_imports', function (Blueprint $table) {
            $table->unsignedBigInteger('product_price_id')->nullable()->after('inventory_id');

            $table->foreign('product_price_id')
                ->references('id')
                ->on('products_prices');
        });
    }
};
