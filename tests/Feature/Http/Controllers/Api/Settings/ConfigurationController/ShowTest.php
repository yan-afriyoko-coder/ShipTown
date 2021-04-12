<?php

namespace Tests\Feature\Http\Controllers\Api\Settings\ConfigurationController;

use App\Models\Configuration;
use App\User;
use Tests\TestCase;

class ShowTest extends TestCase
{
    /** @test */
    public function test_show_call_returns_ok()
    {
        Configuration::query()->forceDelete();
        $configuration = factory(Configuration::class)->create();
        $user = factory(User::class)->create();
        $user->assignRole('admin');

        $route = route('configuration.show', [$configuration->key]);

        $response = $this->actingAs($user, 'api')->getJson($route);

        $response->assertOk();
    }
}
