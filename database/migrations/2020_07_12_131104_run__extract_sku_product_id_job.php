<?php

use App\Models\RmsapiProductImport;
use App\Modules\Rmsapi\src\Jobs\ExtractSkuAndProductIdJob;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RunExtractSkuProductIdJob extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (RmsapiProductImport::query()
            ->whereNotNull('when_processed')
            ->whereNull('sku')
            ->exists())
        {
            ExtractSkuAndProductIdJob::dispatch();
        };

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
