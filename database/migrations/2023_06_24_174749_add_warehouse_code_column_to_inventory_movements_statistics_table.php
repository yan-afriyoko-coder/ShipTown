<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_movements_statistics', function (Blueprint $table) {
            $table->string('warehouse_code')->after('warehouse_id')->index();
        });
    }
};
