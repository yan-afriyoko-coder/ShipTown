<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToModulesMagento2apiProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules_magento2api_products', function (Blueprint $table) {
            $table->boolean('is_in_stock')->nullable()->after('product_id');
            $table->decimal('quantity', 20)->nullable()->after('quantity');
        });
    }
}
