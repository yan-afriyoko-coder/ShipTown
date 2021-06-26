<?php

namespace Tests\Feature\Modules\AutoStatusPaid;

use App\Events\Order\OrderUpdatedEvent;
use App\Models\Order;
use App\Modules\AutoStatusPaid\src\AutoStatusPaidServiceProvider;
use App\Modules\AutoStatusPaid\src\Jobs\SetPaidStatusJob;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class AutoStatusPaidServiceProviderTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_basic_functionality()
    {
        AutoStatusPaidServiceProvider::enableModule();

        Bus::fake();

        $order = factory(Order::class)->create(['is_active' => true]);

        OrderUpdatedEvent::dispatch($order);

        Bus::assertDispatched(SetPaidStatusJob::class);
    }
}
