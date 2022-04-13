<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class PutOrdersOnHoldFromHardcodedToFollowStatusList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Models\Order::query()->whereIn('status_code', [
                'processing',
                'unshipped',
                'partially_shipped',
                'holded',
                'on_hold',
                'missing_item',
                'auto_missing_item',
                'ready'
            ])
            ->update(['is_on_hold' => true]);
    }
}
