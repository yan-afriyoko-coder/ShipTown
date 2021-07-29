<?php

use App\Modules\AutoClose\src\AutoCloseServiceProvider;
use Illuminate\Database\Migrations\Migration;

class RemoveAutocloseModuleDatabaseEntry extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        AutoCloseServiceProvider::uninstallModule();
    }
}
