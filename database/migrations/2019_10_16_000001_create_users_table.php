<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('users')) {
            return;
        }

        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('printer_id')->nullable();
            $table->string('name');
            $table->string('email')->unique();
            $table->foreignId('warehouse_id')->nullable(true);
            $table->foreignId('location_id')->nullable(true);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('address_label_template')->nullable()->after('printer_id');
            $table->boolean('ask_for_shipping_number')->default(true)->after('address_label_template');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('two_factor_code')->after('password')->nullable();
            $table->dateTime('two_factor_expires_at')->after('two_factor_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
