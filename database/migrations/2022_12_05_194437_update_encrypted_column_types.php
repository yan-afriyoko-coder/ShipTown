<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEncryptedColumnTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders_addresses', function (Blueprint $table) {
            $table->longText('first_name_encrypted')->nullable()->change();
            $table->longText('last_name_encrypted')->nullable()->change();
            $table->longText('email_encrypted')->nullable()->change();
            $table->longText('phone_encrypted')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
