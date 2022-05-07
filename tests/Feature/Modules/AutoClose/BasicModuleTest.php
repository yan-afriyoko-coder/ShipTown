<?php

namespace Tests\Feature\Modules\AutoClose;

use App\Events\Order\OrderUpdatedEvent;
use App\Models\Order;
use App\Modules\AutoClose\src\AutoCloseServiceProvider;
use App\Modules\AutoClose\src\Jobs\OpenCloseOrderJob;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class BasicModuleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_if_dispatches_jobs()
    {
        AutoCloseServiceProvider::enableModule();

        Bus::fake();

        $order = factory(Order::class)->create();

        OrderUpdatedEvent::dispatch($order);

        Bus::assertDispatched(OpenCloseOrderJob::class);
    }
}
