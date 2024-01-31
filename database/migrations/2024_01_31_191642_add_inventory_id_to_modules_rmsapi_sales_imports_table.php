<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('modules_rmsapi_sales_imports', function (Blueprint $table) {
            $table->unsignedBigInteger('inventory_id')->nullable()->after('connection_id');

            $table->foreign('inventory_id')
                ->references('id')
                ->on('inventory');
        });
    }
};
