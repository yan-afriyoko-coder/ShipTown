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
        if (Schema::hasTable('modules_dpd-ireland_configuration')) {
            return;
        }

        Schema::create('modules_dpd-ireland_configuration', function (Blueprint $table) {
            $table->id();
            $table->boolean('live')->nullable(false)->default(false);
            $table->string('token');
            $table->string('user');
            $table->string('password');
            $table->string('contact')->nullable(false)->default('');
            $table->string('contact_telephone')->nullable(false)->default('');
            $table->string('contact_email')->nullable(false)->default('');
            $table->string('business_name')->nullable(false)->default('');
            $table->string('address_line_1')->nullable(false)->default('');
            $table->string('address_line_2')->nullable(false)->default('');
            $table->string('address_line_3')->nullable(false)->default('');
            $table->string('address_line_4')->nullable(false)->default('');
            $table->string('country_code', 10)->nullable(false)->default('');
            $table->timestamps();
        });
    }
}
