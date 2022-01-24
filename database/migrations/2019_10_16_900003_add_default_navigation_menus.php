<?php

use App\Models\NavigationMenu;
use Illuminate\Database\Migrations\Migration;

class AddDefaultNavigationMenus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (NavigationMenu::query()->exists()) {
            return;
        }

        NavigationMenu::query()->create([
            'name' => 'Status: picking',
            'url' => '/picklist?order.status_code=picking',
            'group' => 'picklist'
        ]);

        NavigationMenu::query()->create([
            'name' => 'Status: packing_web',
            'url' => '/autopilot/packlist?status=packing_web&sort=order_placed_at',
            'group' => 'packlist'
        ]);

        NavigationMenu::query()->create([
            'name' => 'Status: single_line_orders',
            'url' => '/autopilot/packlist?status=single_line_orders&sort=order_placed_at',
            'group' => 'packlist'
        ]);
    }
}
