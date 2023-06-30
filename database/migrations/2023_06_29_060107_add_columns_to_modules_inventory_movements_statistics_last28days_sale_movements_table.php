<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('modules_inventory_movements_statistics_last28days_sale_movements', function (Blueprint $table) {
            $table->boolean('included_in_7days')->nullable()->index('is_within_7days');
            $table->boolean('included_in_14days')->nullable()->index('is_within_14days');
            $table->boolean('included_in_28days')->nullable()->index('is_within_28days');
        });
    }
};
