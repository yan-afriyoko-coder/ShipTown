<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWarehouseCodeColumnToPicksTable extends Migration
{
    public function up(): void
    {
        Schema::table('picks', function (Blueprint $table) {
            $table->string('warehouse_code')->nullable()->after('product_id');
        });
    }
}
