<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stocktake_suggestions', function (Blueprint $table) {
            $table->index(['inventory_id', 'reason']);
        });
    }
};
