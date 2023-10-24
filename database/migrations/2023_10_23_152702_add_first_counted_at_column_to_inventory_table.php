<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory', function (Blueprint $table) {
            $table->dateTime('first_counted_at')->nullable()->after('last_sold_at');

            $table->index('first_counted_at');
        });
    }
};
