<?php

use Illuminate\Database\Migrations\Migration;

class EnsureOrdersProductsTotalsIsUpToDate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        App\Modules\OrderTotals\src\Jobs\EnsureAllRecordsExistsJob::dispatchNow();
        App\Modules\OrderTotals\src\Jobs\EnsureCorrectTotalsJob::dispatchNow();
    }
}
