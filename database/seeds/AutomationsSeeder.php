<?php

use App\Events\Order\ActiveOrderCheckEvent;
use App\Events\Order\OrderCreatedEvent;
use App\Modules\Automations\src\Actions\Order\SetStatusCodeAction;
use App\Modules\Automations\src\Conditions\Order\ShippingMethodCodeEqualsOrderCondition;
use App\Modules\Automations\src\Conditions\Order\StatusCodeEqualsOrderCondition;
use App\Modules\Automations\src\Models\Action;
use App\Modules\Automations\src\Models\Automation;
use App\Modules\Automations\src\Models\Condition;
use Illuminate\Database\Seeder;

class AutomationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /** @var Automation $automation */
        $automation = Automation::create([
            'name' => 'Store Pickup',
            'priority' => 1,
            'event_class' => ActiveOrderCheckEvent::class,
        ]);

        Condition::create([
            'automation_id' => $automation->getKey(),
            'condition_class' => StatusCodeEqualsOrderCondition::class,
            'condition_value' => 'paid'
        ]);

        Condition::create([
            'automation_id' => $automation->getKey(),
            'condition_class' => ShippingMethodCodeEqualsOrderCondition::class,
            'condition_value' => 'store_pickup'
        ]);

        Action::create([
            'automation_id' => $automation->getKey(),
            'priority' => 1,
            'action_class' => SetStatusCodeAction::class,
            'action_value' => 'store_pickup',
        ]);

        $automation->update(['enabled' => true]);
    }
}
