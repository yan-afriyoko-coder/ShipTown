<?php

namespace Tests\Feature\Settings;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Laravel\Passport\Passport;
use Tests\TestCase;

class OrderStatusesTest extends TestCase
{
    use RefreshDatabase;

    public function test_setting_order_statuses_page_can_be_rendered()
    {
        $this->actingAs(
            factory(User::class)->states('admin')->create()
        );

        $response = $this->get(route('settings.order_statuses'));

        $response->assertStatus(200);

    }

    public function test_access_setting_order_statuses_page_should_be_loggedin()
    {
        $response = $this->get(route('settings.order_statuses'));

        $response->assertRedirect(route('login'));
    }

    public function test_access_setting_order_statuses_page_should_loggedin_as_admin()
    {
        $this->actingAs(
            factory(User::class)->create()
        );

        $response = $this->get(route('settings.order_statuses'));

        $response->assertForbidden();
    }
}
