<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCacheLocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cache_locks', function (Blueprint $table) {
            $table->id();
            $table->string('key');
            $table->integer('key_id');
            $table->dateTime('expires_at');

            $table->index('key');
            $table->unique(['key', 'key_id']);
        });
    }
}
