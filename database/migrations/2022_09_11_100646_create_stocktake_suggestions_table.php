<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStocktakeSuggestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stocktake_suggestions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_id')->references('id')->on('inventory')->cascadeOnDelete();
            $table->integer('points');
            $table->string('reason');
            $table->timestamps();
        });
    }
}
