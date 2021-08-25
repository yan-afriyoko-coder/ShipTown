<?php

use App\Models\Configuration;
use Illuminate\Database\Migrations\Migration;

class ImportConfigurationBusinessNameFromEnv extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Configuration::query()->updateOrCreate([],['business_name' => config('app.tenant_name')]);
    }
}
