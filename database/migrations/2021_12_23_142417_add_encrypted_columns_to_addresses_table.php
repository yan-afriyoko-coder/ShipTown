<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEncryptedColumnsToAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_addresses', function (Blueprint $table) {
            $table->string('first_name_encrypted')->nullable()->after('first_name');
            $table->string('last_name_encrypted')->nullable()->after('last_name');
            $table->string('phone_encrypted')->nullable()->after('phone');
            $table->string('email_encrypted')->nullable()->after('email');
        });
    }
}
