<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('modules_magento2msi_inventory_source_items', function (Blueprint $table) {
            $table->string('magento_type_id')->nullable()->after('magento_product_id');
        });
    }
};
