<?php

namespace Tests\Feature\Http\Controllers\Api\Settings\ModuleConfigurationController;

use App\Models\Module;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    private function simulationTest()
    {
        $module = Module::create(['service_provider_class' => 'Test Module', 'enabled' => 1]);
        $response = $this->put(route('api.settings.modules.update', $module));

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
