<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use App\User;
use Tests\TestCase;

class SettingModulesTest extends TestCase
{
    public function test_setting_modules_page_can_be_rendered()
    {
        Passport::actingAs(
            factory(User::class)->states('admin')->create()
        );
        $response = $this->get(route('settings.module'));

        $response->assertStatus(200);
    }

    public function test_access_setting_modules_page_should_be_loggedin()
    {
        $response = $this->get(route('settings.module'));

        $response->assertRedirect(route('login'));
    }

    public function test_access_setting_modules_page_should_loggedin_as_admin()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );
        $response = $this->get(route('settings.module'));

        $response->assertForbidden();
    }
}
