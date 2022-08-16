<?php

use App\Modules\Api2cart\src\Models\Api2cartOrderImports;
use Illuminate\Database\Migrations\Migration;

class DeleteOldApi2cartOrderImports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Api2cartOrderImports::where('when_processed', '<', now()->subDays(1))
            ->delete();
    }
}
