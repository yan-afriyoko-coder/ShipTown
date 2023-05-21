<?php

use App\Modules\Telescope\src\TelescopeModuleServiceProvider;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        TelescopeModuleServiceProvider::installModule();
    }
};
