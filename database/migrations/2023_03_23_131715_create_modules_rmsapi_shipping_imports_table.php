<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('modules_rmsapi_shipping_imports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('connection_id');
            $table->json('raw_import');
            $table->timestamps();

            $table->foreign('connection_id')
                ->references('id')
                ->on('modules_rmsapi_connections')
                ->onDelete('cascade');
        });
    }
};
