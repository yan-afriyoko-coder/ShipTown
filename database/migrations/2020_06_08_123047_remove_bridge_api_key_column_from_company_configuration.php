<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveBridgeApiKeyColumnFromCompanyConfiguration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $config = \App\Models\CompanyConfiguration::query()->first();

        if(isset($config->bridge_api_key)) {
            $config_new = \App\Models\ConfigurationApi2cart::query()->firstOrCreate([]);

            $config_new->bridge_api_key = $config->bridge_api_key;

            $config_new->save();
        };

        Schema::table('company_configuration', function (Blueprint $table) {
            $table->dropColumn('bridge_api_key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('company_configuration', function (Blueprint $table) {
            $table->string('bridge_api_key')->nullable(true);
        });
    }
}
