<?php

use App\Modules\NonInventoryProductTag\src\NonInventoryProductTagServiceProvider;
use Illuminate\Database\Migrations\Migration;

class InstallNonInventoryProductTagModule extends Migration
{
    public function up()
    {
        NonInventoryProductTagServiceProvider::installModule();
    }
}
