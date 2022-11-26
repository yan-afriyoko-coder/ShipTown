<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductIdColumnToStocktakeSuggestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stocktake_suggestions', function (Blueprint $table) {
            $table->foreignId('product_id')
                ->nullable()
                ->after('inventory_id')
                ->references('id')->on('products')->cascadeOnDelete();
        });
    }
}
