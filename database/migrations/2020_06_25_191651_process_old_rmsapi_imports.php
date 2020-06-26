<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ProcessOldRmsapiImports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $batches = \App\Models\RmsapiProductImport::query()
            ->whereNull('when_processed')
            ->whereDate('created_at', '<' , \Carbon\Carbon::today()->subDays(7))
            ->distinct()
            ->get('batch_uuid');


        foreach ($batches as $batch) {
            \App\Jobs\Rmsapi\ProcessImportedProductsJob::dispatch(
                \Ramsey\Uuid\Uuid::fromString($batch->batch_uuid)
            );
        }
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
