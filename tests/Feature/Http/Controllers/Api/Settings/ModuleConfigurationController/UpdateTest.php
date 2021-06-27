<?php

namespace Tests\Feature\Http\Controllers\Api\Settings\ModuleConfigurationController;

use App\Module;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_update_call_returns_ok()
    {
        Passport::actingAs(
            factory(User::class)->states('admin')->create()
        );

        $module = Module::create(['service_provider_class' => 'Test Module', 'enabled' => 1]);
        $response = $this->put(route('api.settings.modules.update', $module));
        $response->assertSuccessful();
    }

    public function test_update_call_should_be_loggedin()
    {
        $response = $this->get(route('api.settings.modules.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_update_call_should_loggedin_as_admin()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        $module = Module::create(['service_provider_class' => 'Test Module', 'enabled' => 1]);
        $response = $this->put(route('api.settings.modules.update', $module));

        $response->assertForbidden();
    }
}
