<?php

namespace Tests\Feature\Http\Controllers\Api\Settings;

use App\Models\Configuration;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Api\Settings\ConfigurationController
 */
class ConfigurationControllerTest extends TestCase
{
    /**
     * @test
     */
    public function show_returns_an_ok_response()
    {
        Configuration::query()->forceDelete();
        $configuration = factory(Configuration::class)->create();
        $user = factory(User::class)->create();
        $user->assignRole('admin');

        $route = route('configuration.show', [$configuration->key]);

        $response = $this->actingAs($user, 'api')->getJson($route);

        $response->assertOk();
        $response->assertJsonStructure([
            // TODO: compare expected response data
        ]);

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function store_returns_an_ok_response()
    {
        Configuration::query()->forceDelete();

        $user = factory(User::class)->create()->assignRole('admin');

        $response = $this->actingAs($user, 'api')->postJson(route('configuration.store'), [
            'key' => 'testKey',
            'value' => 'testValue',
        ]);

        $response->assertSuccessful();

        $response->assertJsonStructure([
            'data' => [
                'id',
                'key',
                'value',
            ]
        ]);
    }

    // test cases...
}
