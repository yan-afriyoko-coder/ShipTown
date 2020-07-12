<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddQuantityPickedColumnToPicklists extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('picklists', function (Blueprint $table) {
            $table->decimal('quantity_picked')
                ->after('quantity_to_pick')
                ->nullable(false)
                ->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('picklists', function (Blueprint $table) {
            $table->dropColumn('quantity_picked');
        });
    }
}
