<?php

use Illuminate\Database\Migrations\Migration;

class ReinstallModules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Services\ModulesService::updateModulesTable();
    }
}
