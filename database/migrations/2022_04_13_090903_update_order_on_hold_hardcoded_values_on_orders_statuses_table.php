<?php

use App\Models\OrderStatus;
use Illuminate\Database\Migrations\Migration;

class UpdateOrderOnHoldHardcodedValuesOnOrdersStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        OrderStatus::query()->whereIn('code', [
                'processing',
                'unshipped',
                'partially_shipped',
                'holded',
                'on_hold',
                'missing_item',
                'auto_missing_item',
                'ready'
            ])
            ->update(['order_on_hold' => true]);
    }
}
