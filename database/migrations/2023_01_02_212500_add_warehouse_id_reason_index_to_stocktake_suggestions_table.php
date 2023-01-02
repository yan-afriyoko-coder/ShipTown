<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWarehouseIdReasonIndexToStocktakeSuggestionsTable extends Migration
{
    public function up()
    {
        Schema::table('stocktake_suggestions', function (Blueprint $table) {
            $table->index(['warehouse_id', 'reason']);
        });
    }
}
