<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeleteOldRmsapiModuleServiceProvider extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     * @throws Exception
     */
    public function up()
    {
        \App\Models\Module::whereServiceProviderClass('App\Modules\Rmsapi\src\EventServiceProviderBase')
            ->forceDelete();
    }
}
