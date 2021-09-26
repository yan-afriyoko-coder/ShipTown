<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNullableOnTableAutomations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules_automations', function (Blueprint $table) {
            $table->string('event_class')->nullable()->change();
        });

        Schema::table('modules_automations_conditions', function (Blueprint $table) {
            $table->string('condition_class')->nullable()->change();
            $table->string('condition_value')->nullable()->change();
        });

        Schema::table('modules_automations_actions', function (Blueprint $table) {
            $table->string('action_class')->nullable()->change();
            $table->string('action_value')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
