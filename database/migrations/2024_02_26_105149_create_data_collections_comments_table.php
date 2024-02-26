<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('data_collections_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('data_collection_id');
            $table->foreignId('user_id')->nullable();
            $table->string('comment');
            $table->timestamps();

            $table->foreign('data_collection_id')
                ->references('id')
                ->on('data_collections')
                ->onDelete('CASCADE');

            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('SET NULL');
        });
    }
};
