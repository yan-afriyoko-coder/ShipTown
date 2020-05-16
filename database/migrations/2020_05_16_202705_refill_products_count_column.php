<?php

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RefillProductsCountColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\Event::fake();

        \App\Models\Order::query()->chunk(500, function ($orders) {

            foreach($orders as $order) {

                $order['products_count'] = 0;

                if (! empty($order['raw_import'])) {
                    if (Arr::has($order['raw_import'], 'order_products')) {

                        foreach ($order['raw_import']['order_products'] as $product) {
                            $order['products_count'] += $product['quantity'];
                        }

                    }
                }

                $order->save();

            };

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
