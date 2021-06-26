<?php

namespace Tests\Feature\Http\Controllers\Api\Settings\Module\Api2cart\Api2cartConnectionController;

use App\User;
use Tests\TestCase;

/**
 * Class IndexTest
 * @package Tests\Feature\Http\Controllers\Api\Settings\Module\Api2cart\Api2cartConnectionController
 */
class IndexTest extends TestCase
{
    /** @test */
    public function test_index_call_returns_ok()
    {
        /** @var User $user **/
        $user = factory(User::class)->create();
        $user->assignRole('admin');

        $response = $this->actingAs($user, 'api')->getJson(route('api.settings.module.api2cart.connections.index'));

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                '*' => [
                ],
            ],
        ]);
    }
}
