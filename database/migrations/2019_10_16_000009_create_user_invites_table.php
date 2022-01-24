<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserInvitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('user_invites')) {
            return;
        }

        Schema::create('user_invites', function (Blueprint $table) {
            $table->increments('id');
            $table->string('email');
            $table->string('token', 16)->unique();
            $table->timestamps();
        });

        Schema::dropIfExists('user_invites');
    }
}
