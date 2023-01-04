<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLastReceivedAtColumnToInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('inventory', 'first_received_at')) {
            return;
        }

        Schema::table('inventory', function (Blueprint $table) {
            $table->dateTime('first_received_at')->nullable()->after('restock_level');
            $table->dateTime('last_received_at')->nullable()->after('first_received_at');
            $table->dateTime('first_sold_at')->nullable()->after('last_received_at');
            $table->dateTime('last_sold_at')->nullable()->after('first_sold_at');
        });
    }
}
