<?php

namespace Tests\Feature\Http\Controllers\Api\Settings\NavigationMenuController;

use App\Models\NavigationMenu;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Passport\Passport;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    use RefreshDatabase;

    private function simulationTest()
    {
        $navigationMenu = NavigationMenu::create([
            'name'  => 'testing',
            'url'   => 'testing',
            'group' => 'picklist',
        ]);

        $response = $this->delete(route('api.settings.navigation-menu.destroy', $navigationMenu));

        return $response;
    }

    /** @test */
    public function test_delete_call_returns_ok()
    {
        Passport::actingAs(
            User::factory()->admin()->create()
        );

        $response = $this->simulationTest();

        $response->assertSuccessful();
    }

    public function test_delete_call_should_be_loggedin()
    {
        $response = $this->simulationTest();

        $response->assertRedirect(route('login'));
    }
}
