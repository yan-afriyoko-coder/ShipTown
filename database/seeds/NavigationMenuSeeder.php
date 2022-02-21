<?php

use App\Models\NavigationMenu;
use Illuminate\Database\Seeder;

class NavigationMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $menu = [
//            [
//                'name'  => 'Web: picking',
//                'url'   => '/picklist?order.status_code=picking&inventory_source_location_id=100',
//                'group' => 'picklist',
//            ],
//            [
//                'name' => 'Warehouse: paid,picking',
//                'url' => '/picklist?order.status_code=paid,picking&inventory_source_location_id=99',
//                'group' => 'picklist',
//            ],
//            [
//                'name' => 'Status: packing_warehouse',
//                'url' => '/autopilot/packlist?inventory_source_location_id=99&status=packing_warehouse&sort=order_placed_at',
//                'group' => 'packlist'
//            ],
//            [
//                'name' => 'Status: single_line_orders',
//                'url' => '/autopilot/packlist?inventory_source_location_id=100&status=single_line_orders&sort=min_shelf_location%2Corder_placed_at',
//                'group' => 'packlist'
//            ],
//            [
//                'name' => 'Status: paid',
//                'url' => '/autopilot/packlist?inventory_source_location_id=1&status=paid&sort=min_shelf_location%2Corder_placed_at',
//                'group' => 'packlist'
//            ],
        ];

        NavigationMenu::insert($menu);
    }
}
