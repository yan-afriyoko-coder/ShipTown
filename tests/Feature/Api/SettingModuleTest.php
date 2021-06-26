<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use App\User;
use App\Models\Module;
use Tests\TestCase;

class SettingModuleTest extends TestCase
{
    public function test_api_list_modules_can_be_loaded()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );
        $response = $this->get(route('api.modules.index'));

        $response->assertSuccessful();
    }

    public function test_api_module_can_be_updated()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        Module::query()->delete();

        $module = Module::create(['name' => 'Test Module', 'is_active' => 1]);
        $response = $this->put(route('api.modules.update', $module));
        $response->assertSuccessful();
    }
}
