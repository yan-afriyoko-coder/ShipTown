<?php

use App\Models\NavigationMenu;
use App\Models\Order;
use Illuminate\Database\Seeder;

class PaidOrdersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(Order::class, 10)
            ->with('orderProducts', 1)
            ->create(['status_code' => 'paid']);

        factory(Order::class, 10)
            ->with('orderProducts', 2)
            ->create(['status_code' => 'paid']);

        factory(Order::class, 10)
            ->with('orderProducts', 3)
            ->create(['status_code' => 'paid']);

        factory(Order::class, 10)
            ->with('orderProducts', 4)
            ->create(['status_code' => 'paid']);

        Order::all()->each(function (Order $order) {
            $order->total_paid = $order->total;
            $order->save();
        });


        $menu = [
            [
                'name' => 'Status: paid',
                'url' => '/picklist?order.status_code=paid',
                'group' => 'picklist',
            ],
            [
                'name' => 'Status: paid',
                'url' => '/autopilot/packlist?&status=paid&sort=min_shelf_location%2Corder_placed_at',
                'group' => 'packlist'
            ],
        ];

        NavigationMenu::insert($menu);
    }
}
