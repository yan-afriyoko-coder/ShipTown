<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWidgetsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('widgets')) {
            return;
        }

        Schema::create('widgets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->json('config');
            $table->timestamps();
        });
    }
}
