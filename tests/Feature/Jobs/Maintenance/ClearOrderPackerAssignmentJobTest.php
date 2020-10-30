<?php

namespace Tests\Feature\Jobs\Maintenance;

use App\Jobs\Maintenance\ClearOrderPackerAssignmentJob;
use App\Models\Order;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Bus;
use Laravel\Passport\Passport;
use Tests\TestCase;

class ClearOrderPackerAssignmentJobTest extends TestCase
{
    public function testIfDoesNotUpdateLessThan12HoursOfInactivity()
    {
        // prepare
        Order::query()->forceDelete();

        $user = factory(User::class)->create();

        factory(Order::class)->create([
            'packed_at' => null,
            'packer_user_id' => $user->getKey(),
            'updated_at' => Carbon::now()->subHours(11),
        ]);

        // act
        Bus::fake();

        ClearOrderPackerAssignmentJob::dispatchNow();

        // assert
        $this->assertFalse(
            Order::whereNull('packer_user_id')
                ->exists()
        );
    }

    /**
     * A basic feature test
     *
     * @return void
     */
    public function testIfClearsPackersAfter12hoursInactivity()
    {
        Order::query()->forceDelete();

        $user = factory(User::class)->create();

        Passport::actingAs($user);

        factory(Order::class)->create([
            'packed_at' => null,
            'packer_user_id' => $user->getKey(),
            'updated_at' => Carbon::now()->subHours(14),
        ]);

        ClearOrderPackerAssignmentJob::dispatchNow();

        $this->assertTrue(
            Order::whereNull('packer_user_id')
                ->exists()
        );
    }
}
