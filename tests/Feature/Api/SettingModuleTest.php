<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use App\User;
use App\Module;
use Tests\TestCase;

class SettingModuleTest extends TestCase
{
    public function test_list_modules_can_be_loaded()
    {
        Passport::actingAs(
            factory(User::class)->states('admin')->create()
        );
        $response = $this->get(route('api.settings.modules.index'));

        $response->assertSuccessful();
    }

    public function test_module_can_be_updated()
    {
        Passport::actingAs(
            factory(User::class)->states('admin')->create()
        );

        Module::query()->delete();

        $module = Module::create(['service_provider_class' => 'Test Module', 'enabled' => 1]);
        $response = $this->put(route('api.settings.modules.update', $module));
        $response->assertSuccessful();
    }

    public function test_load_list_modules_should_be_loggedin()
    {
        $response = $this->get(route('api.settings.modules.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_load_list_module_should_loggedin_as_admin()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        $module = Module::create(['service_provider_class' => 'Test Module', 'enabled' => 1]);
        $response = $this->put(route('api.settings.modules.update', $module));

        $response->assertForbidden();
    }

    public function test_update_modules_should_be_loggedin()
    {
        $response = $this->get(route('api.settings.modules.index'));

        $response->assertRedirect(route('login'));
    }


    public function test_update_module_should_loggedin_as_admin()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        $module = Module::create(['service_provider_class' => 'Test Module', 'enabled' => 1]);
        $response = $this->put(route('api.settings.modules.update', $module));

        $response->assertForbidden();
    }
}
