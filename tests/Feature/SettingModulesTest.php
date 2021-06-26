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
            factory(User::class)->create()
        );
        $response = $this->get(route('settings.module'));

        $response->assertStatus(200);
    }
}
