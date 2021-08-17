<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModulesAutomationsConditionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('modules_automations_conditions')) {
            return;
        }

        Schema::create('modules_automations_conditions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('automation_id');
            $table->string('validation_class')->nullable(false);
            $table->string('condition_value')->nullable(false)->default('');
            $table->timestamps();
        });
    }
}
