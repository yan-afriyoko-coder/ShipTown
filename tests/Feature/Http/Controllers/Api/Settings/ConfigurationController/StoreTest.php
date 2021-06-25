<?php

namespace Tests\Feature\Http\Controllers\Api\Settings\ConfigurationController;

use App\Models\Configuration;
use App\User;
use Tests\TestCase;

class StoreTest extends TestCase
{
    /** @test */
    public function test_store_call_returns_ok()
    {
        Configuration::query()->forceDelete();

        $user = factory(User::class)->create()->assignRole('admin');

        $response = $this->actingAs($user, 'api')->postJson(route('configuration.store'), [
            'key'   => 'testKey',
            'value' => 'testValue',
        ]);

        $response->assertSuccessful();

        $response->assertJsonStructure([
            'data' => [
                'id',
                'key',
                'value',
            ],
        ]);
    }
}
