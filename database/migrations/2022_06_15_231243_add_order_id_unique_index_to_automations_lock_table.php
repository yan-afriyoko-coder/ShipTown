<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrderIdUniqueIndexToAutomationsLockTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('modules_automations_order_lock', function (Blueprint $table) {
            $table->foreignId('order_id')->unique()->change();
        });
    }
}
