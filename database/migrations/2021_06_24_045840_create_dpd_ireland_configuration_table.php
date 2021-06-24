<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDpdIrelandConfigurationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('modules_dpd-ireland_configuration', function (Blueprint $table) {
            $table->id();
            $table->boolean('live')->default(false);
            $table->string('token');
            $table->string('user');
            $table->string('password');
            $table->string('contact');
            $table->string('contact_telephone');
            $table->string('contact_email')->nullable();
            $table->string('business_name')->nullable();
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('address_line_3');
            $table->string('address_line_4');
            $table->string('country_code', 10);
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
        Schema::dropIfExists('modules_dpd-ireland_configuration');
    }
}
