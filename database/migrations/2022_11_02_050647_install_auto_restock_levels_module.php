<?php

use App\Modules\AutoRestockLevels\src\AutoRestockLevelsServiceProvider;
use Illuminate\Database\Migrations\Migration;

class InstallAutoRestockLevelsModule extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        AutoRestockLevelsServiceProvider::installModule();
    }
}
