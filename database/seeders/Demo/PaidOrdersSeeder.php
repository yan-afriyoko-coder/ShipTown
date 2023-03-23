<?php

namespace Database\Seeders\Demo;

use App\Models\NavigationMenu;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Modules\Automations\src\Actions\Order\SetStatusCodeAction;
use App\Modules\Automations\src\Conditions\Order\IsFullyPackedCondition;
use App\Modules\Automations\src\Conditions\Order\StatusCodeEqualsCondition;
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
        $this->createOrders();
        $this->createPaidToCompleteAutomation();
    }

    private function createOrders(): void
    {
        Order::factory()
            ->count(10)
            ->create(['status_code' => 'paid'])
            ->each(function (Order $order) {
                OrderProduct::factory()->count(1)->create(['order_id' => $order->getKey()]);
            });

        Order::factory()
            ->count(10)
            ->create(['status_code' => 'paid'])
            ->each(function (Order $order) {
                OrderProduct::factory()->count(2)->create(['order_id' => $order->getKey()]);
            });

        Order::factory()
            ->count(10)
            ->create(['status_code' => 'paid'])
            ->each(function (Order $order) {
                OrderProduct::factory()->count(3)->create(['order_id' => $order->getKey()]);
            });

        Order::factory()
            ->count(10)
            ->create(['status_code' => 'paid'])
            ->each(function (Order $order) {
                OrderProduct::factory()->count(4)->create(['order_id' => $order->getKey()]);
            });

        Order::query()->get()
            ->each(function (Order $order) {
                $order->total_paid = $order->total;
                $order->save();
            });
    }

    private function createPaidToCompleteAutomation(): void
    {
        /** @var Automation $automation */
        $automation = Automation::create([
            'name' => 'paid to complete',
            'enabled' => false,
        ]);

        $automation->conditions()->create([
            'condition_class' => StatusCodeEqualsCondition::class,
            'condition_value' => 'paid'
        ]);

        $automation->conditions()->create([
            'condition_class' => IsFullyPackedCondition::class,
            'condition_value' => 'True'
        ]);

        $automation->actions()->create([
            'action_class' => SetStatusCodeAction::class,
            'action_value' => 'complete'
        ]);

        $automation->update(['enabled' => true]);
    }
}
