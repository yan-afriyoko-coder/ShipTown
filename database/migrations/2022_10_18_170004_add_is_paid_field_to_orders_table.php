<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsPaidFieldToOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->boolean('is_fully_paid')
                ->storedAs('total_paid >= total - total_discounts')
                ->comment('total_paid >= total - total_discounts')
                ->after('is_editing');
        });
    }
}
