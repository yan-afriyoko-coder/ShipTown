<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModulesAutomationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('modules_automations')) {
            return;
        }

        Schema::create('modules_automations', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('priority')->nullable(false)->default(0);
            $table->boolean('enabled')->nullable(false)->default(false);
            $table->string('name')->nullable(false);
            $table->string('event_class');
            $table->timestamps();
        });
    }
}
