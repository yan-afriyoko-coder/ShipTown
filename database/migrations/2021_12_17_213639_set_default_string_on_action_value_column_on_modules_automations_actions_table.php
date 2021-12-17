<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SetDefaultStringOnActionValueColumnOnModulesAutomationsActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules_automations_actions', function (Blueprint $table) {
           $table->string('action_value')->default('')->change();
        });
    }
}
