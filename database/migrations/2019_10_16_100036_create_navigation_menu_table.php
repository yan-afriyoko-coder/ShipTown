<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNavigationMenuTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('navigation_menu')) {
            return;
        }

        Schema::create('navigation_menu', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('url', 255);
            $table->string('group', 100);
            $table->timestamps();
        });
    }
}
