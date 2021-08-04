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
            [
                'name' => 'Web: picking',
                'url' => '/picklist?order.status_code=picking&inventory_source_location_id=100',
                'group' => 'picklist',
            ],
            [
                'name' => 'Warehouse: paid,picking',
                'url' => '/picklistorder.status_code=paid,picking&inventory_source_location_id=99',
                'group' => 'picklist',
            ],
            [
                'name' => 'Status: single_line_orders',
                'url' => '/packlist?inventory_source_location_id=100&status=single_line_orders&sort=min_shelf_location',
                'group' => 'packlist'
            ]
        ];

        NavigationMenu::insert($menu);
    }
}
