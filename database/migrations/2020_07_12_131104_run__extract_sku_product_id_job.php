<?php

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
        ExtractSkuAndProductIdJob::dispatch();
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
