<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_totals_by_warehouse_tag', function (Blueprint $table) {
            $table->boolean('recalc_required')->default(1)->after('tag_id');

            $table->index('recalc_required');
        });
    }
};
