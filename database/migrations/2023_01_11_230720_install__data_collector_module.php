<?php

use App\Modules\DataCollector\src\DataCollectorServiceProvider;
use Illuminate\Database\Migrations\Migration;

class InstallDataCollectorModule extends Migration
{
    public function up()
    {
        DataCollectorServiceProvider::installModule();
    }
}
