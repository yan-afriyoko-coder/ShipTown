<?php

namespace Tests\Feature\Modules\Webhooks;

use App\Events\EveryDayEvent;
use App\Events\EveryFiveMinutesEvent;
use App\Events\EveryHourEvent;
use App\Events\EveryMinuteEvent;
use App\Events\EveryTenMinutesEvent;
use App\Modules\Webhooks\src\WebhooksServiceProviderBase;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        WebhooksServiceProviderBase::enableModule();
    }

    /** @test */
    public function testBasicFunctionality()
    {
        if (empty(env('SQS_QUEUE'))) {
            $this->markTestSkipped('SQS_QUEUE is not set.');
        }

        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /** @test */
    public function testIfNoErrorsDuringEvents()
    {
        EveryMinuteEvent::dispatch();
        EveryFiveMinutesEvent::dispatch();
        EveryTenMinutesEvent::dispatch();
        EveryHourEvent::dispatch();
        EveryDayEvent::dispatch();

        $this->assertTrue(true, 'Errors encountered while dispatching events');
    }
}
