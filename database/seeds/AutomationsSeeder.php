<?php

use App\Events\Order\OrderCreatedEvent;
use App\Modules\Automations\src\Executors\Order\SetStatusCodeExecutor;
use App\Modules\Automations\src\Models\Automation;
use App\Modules\Automations\src\Models\Condition;
use App\Modules\Automations\src\Models\Execution;
use App\Modules\Automations\src\Validators\Order\ShippingMethodCodeEqualsValidator;
use App\Modules\Automations\src\Validators\Order\StatusCodeEqualsValidator;
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
        $automation = Automation::create([
            'name' => 'Store Pickup',
            'priority' => 1,
            'event_class' => OrderCreatedEvent::class,
            'enabled' => true,
        ]);

        Condition::create([
            'automation_id' => $automation->getKey(),
            'validation_class' => StatusCodeEqualsValidator::class,
            'condition_value' => 'paid'
        ]);

        Condition::create([
            'automation_id' => $automation->getKey(),
            'validation_class' => ShippingMethodCodeEqualsValidator::class,
            'condition_value' => 'store_pickup'
        ]);

        Execution::create([
            'automation_id' => $automation->getKey(),
            'priority' => 1,
            'execution_class' => SetStatusCodeExecutor::class,
            'execution_value' => 'store_pickup',
        ]);
    }
}
