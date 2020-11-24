<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrderAddressesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('order_addresses')) {
            return;
        }

        Schema::create('order_addresses', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string("company")->default('');
            $table->string("gender")->default('');
            $table->string("first_name")->default('');
            $table->string("last_name")->default('');
            $table->string("address1")->default('');
            $table->string("address2")->default('');
            $table->string("postcode")->default('');
            $table->string("city")->default('');
            $table->string("state_code")->default('');
            $table->string("state_name")->default('');
            $table->string("country_code")->default('');
            $table->string("country_name")->default('');
            $table->string("phone")->default('');
            $table->string("fax")->default('');
            $table->string("website")->default('');
            $table->string("region")->default('');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('order_addresses');
    }
}
