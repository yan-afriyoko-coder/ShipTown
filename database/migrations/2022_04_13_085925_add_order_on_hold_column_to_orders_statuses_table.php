<?php

use App\Models\OrderStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrderOnHoldColumnToOrdersStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('orders_statuses', function (Blueprint $table) {
            $table->boolean('order_on_hold')->default(false)->after('order_active');
        });
    }
}
