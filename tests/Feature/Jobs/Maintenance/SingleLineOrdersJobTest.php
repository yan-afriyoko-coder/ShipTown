<?php

namespace Tests\Feature\Jobs\Maintenance;

use App\Jobs\Maintenance\SingleLineOrdersJob;
use App\Models\Order;
use App\User;
use Illuminate\Support\Facades\Bus;
use Laravel\Passport\Passport;
use Tests\TestCase;

class SingleLineOrdersJobTest extends TestCase
{
    public function testIfJobIsDispatched()
    {
        Bus::fake();

        Passport::actingAs(
            factory(User::class)->create()
        );

        $response = $this->get('/api/run/maintenance');

        $response->assertStatus(200);

        Bus::assertDispatched(SingleLineOrdersJob::class);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIfJobMovesOrders()
    {
        Order::query()->forceDelete();

        factory(Order::class)
            ->with('orderProducts')
            ->create(['status_code' => 'paid']);

        SingleLineOrdersJob::dispatchNow();

        $this->assertDatabaseMissing('orders', [
            'status_code' => 'paid',
            'product_line_count' => 1
        ]);
    }
}
