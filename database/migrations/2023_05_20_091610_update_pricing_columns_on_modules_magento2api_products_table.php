<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdatePricingColumnsOnModulesMagento2apiProductsTable extends Migration
{
    public function up(): void
    {
        Schema::table('modules_magento2api_products', function (Blueprint $table) {
            $table->decimal('magento_price', 20)->nullable()->change();
            $table->decimal('magento_sale_price', 20)->nullable()->change();
        });
    }
}
