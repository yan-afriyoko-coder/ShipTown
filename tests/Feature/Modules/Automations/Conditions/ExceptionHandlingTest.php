<?php

namespace Tests\Feature\Modules\Automations\Conditions;

use App\Events\HourlyEvent;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Modules\Automations\src\Jobs\RunAutomationsOnActiveOrdersJob;
use App\Modules\Automations\src\Models\Automation;
use App\Modules\Automations\src\Models\Condition;
use App\Modules\Automations\tests\Feature\Conditions\ExceptionTestingConditionOnlyForTestingPurposes;
use Mockery\Exception;
use Tests\TestCase;

class ExceptionHandlingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        OrderStatus::query()->forceDelete();
        Automation::query()->forceDelete();

        $automation = factory(Automation::class)->create(['event_class' => 'App\Events\Order\ActiveOrderCheckEvent']);

        factory(Condition::class)->create([
            'automation_id'     => $automation->getKey(),
            'condition_class'   => ExceptionTestingConditionOnlyForTestingPurposes::class,
            'condition_value'   => ''
        ]);

        $automation->update(['enabled' => true]);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        try {
            factory(Order::class)->create();

            RunAutomationsOnActiveOrdersJob::dispatch();
        } catch (Exception $exception) {
            $this->fail('Exceptions: '. $exception->getMessage());
        }

        $this->assertTrue(true, 'We just make sure no exceptions returned');
    }
}


