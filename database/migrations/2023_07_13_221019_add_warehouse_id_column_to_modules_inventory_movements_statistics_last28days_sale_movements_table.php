<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('modules_inventory_movements_statistics_last28days_sale_movements', function (Blueprint $table) {
            $table->unsignedBigInteger('warehouse_id')->nullable()->after('inventory_id');
        });
    }
};
