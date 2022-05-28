<?php

use App\Events\Order\ActiveOrderCheckEvent;
use App\Models\NavigationMenu;
use App\Models\Order;
use App\Modules\Automations\src\Actions\Order\SetStatusCodeAction;
use App\Modules\Automations\src\Conditions\Order\IsFullyPackedOrderCondition;
use App\Modules\Automations\src\Conditions\Order\StatusCodeEqualsOrderCondition;
use App\Modules\Automations\src\Models\Automation;
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
        factory(Order::class, 1)
            ->with('orderProducts', 1)
            ->create(['status_code' => 'paid']);

        $this->createOrders();
        $this->createNavigationMenu();
        $this->createPaidToCompleteAutomation();
    }

    private function createOrders(): void
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

        Order::query()->get()
            ->each(function (Order $order) {
                $order->total_paid = $order->total;
                $order->save();
            });
    }

    private function createNavigationMenu(): void
    {
        $menu = [
            [
                'name' => 'Status: paid',
                'url' => '/picklist?order.status_code=paid',
                'group' => 'picklist',
            ],
            [
                'name' => 'Status: paid',
                'url' => '/autopilot/packlist?&status=paid&sort=inventory_source_shelf_location,order_placed_at',
                'group' => 'packlist'
            ],
        ];

        NavigationMenu::insert($menu);
    }

    private function createPaidToCompleteAutomation(): void
    {
        /** @var Automation $automation */
        $automation = Automation::create([
            'name' => 'paid to complete',
            'event_class' => ActiveOrderCheckEvent::class,
            'enabled' => false,
        ]);

        $automation->conditions()->create([
            'condition_class' => StatusCodeEqualsOrderCondition::class,
            'condition_value' => 'paid'
        ]);

        $automation->conditions()->create([
            'condition_class' => IsFullyPackedOrderCondition::class,
            'condition_value' => 'True'
        ]);

        $automation->actions()->create([
            'action_class' => SetStatusCodeAction::class,
            'action_value' => 'complete'
        ]);

        $automation->update(['enabled' => true]);
    }
}
