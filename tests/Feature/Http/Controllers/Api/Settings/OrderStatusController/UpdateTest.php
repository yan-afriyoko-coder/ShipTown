<?php

namespace Tests\Feature\Http\Controllers\Api\Settings\OrderStatusController;

use App\Models\OrderStatus;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    private function simulationTest() {
        $orderStatus = OrderStatus::create(['name' => 'testing', 'code' => 'testing', 'order_active' => 1, 'reserves_stock' => 1]);
        $response = $this->put(route('api.settings.order-statuses.update', $orderStatus), ['order_active' => 0, 'reserves_stock' => 0]);

        return $response;
    }

    /** @test */
    public function test_update_call_returns_ok()
    {
        Passport::actingAs(
            factory(User::class)->states('admin')->create()
        );

        $response = $this->simulationTest();

        $response->assertSuccessful();
    }

    public function test_update_call_should_be_loggedin()
    {
        $response = $this->simulationTest();

        $response->assertRedirect(route('login'));
    }

    public function test_update_call_should_loggedin_as_admin()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        $response = $this->simulationTest();

        $response->assertForbidden();
    }
}
