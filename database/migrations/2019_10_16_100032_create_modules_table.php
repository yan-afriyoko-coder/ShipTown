<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('modules')) {
            return;
        }

        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('service_provider_class')->nullable(false);
            $table->boolean('enabled')->nullable(false)->default(false);
            $table->timestamps();
        });
    }
}
