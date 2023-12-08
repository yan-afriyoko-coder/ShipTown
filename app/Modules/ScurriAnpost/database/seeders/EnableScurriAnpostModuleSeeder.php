<?php

namespace App\Modules\ScurriAnpost\database\seeders;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Modules\ScurriAnpost\src\ScurriServiceProvider;
use Illuminate\Database\Seeder;

class EnableScurriAnpostModuleSeeder extends Seeder
{
    public function run()
    {
        if (empty(env('SCURRI_BASE_URI')) or empty(env('SCURRI_USERNAME')) or empty(env('SCURRI_PASSWORD'))) {
            return;
        }

        ScurriServiceProvider::enableModule();

        /** @var Order $order */
        $order = Order::factory()->create(['status_code' => 'anpost_courier', 'label_template' => 'anpost_3day']);

        OrderProduct::factory()->create(['order_id' => $order->getKey()]);

        $order->refresh();

        $order->update(['total_paid' => $order->total_order]);
    }
}
