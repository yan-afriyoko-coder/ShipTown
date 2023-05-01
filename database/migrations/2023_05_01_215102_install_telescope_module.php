<?php

use App\Modules\Telescope\src\TelescopeModuleServiceProvider;
use Illuminate\Database\Migrations\Migration;

class InstallTelescopeModule extends Migration
{
    public function up(): void
    {
        TelescopeModuleServiceProvider::installModule();
    }
}
