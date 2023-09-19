<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('heartbeats', function (Blueprint $table) {
            $table->string('level')->default('error')->after('code');

            $table->index('level');
        });
    }
};
