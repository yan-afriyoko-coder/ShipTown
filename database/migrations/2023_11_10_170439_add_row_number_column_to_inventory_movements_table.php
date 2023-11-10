<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventory_movements', function (Blueprint $table) {
            $table->unsignedInteger('sequence_number')->nullable()->after('occurred_at')->comment('row_number() over (partition by inventory_id order by occurred_at asc, id asc)');

            $table->unique(['inventory_id', 'sequence_number']);
        });
    }
};
