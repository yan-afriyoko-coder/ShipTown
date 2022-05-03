<?php

use Illuminate\Database\Migrations\Migration;

class UninstallOversoldProductNotificationModule extends Migration
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
