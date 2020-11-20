<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TransferQuantityNotPickedOnOrderProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\OrderProduct::where('quantity_not_picked', '>', 0)
            ->each(function ($orderProduct) {
                $orderProduct->update([
                    'quantity_skipped_picking' => $orderProduct->quantity_not_picked
                ]);
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
