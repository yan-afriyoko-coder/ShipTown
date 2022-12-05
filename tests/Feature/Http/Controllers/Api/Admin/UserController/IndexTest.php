<?php

namespace Tests\Feature\Http\Controllers\Api\Admin\UserController;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_index_call_returns_ok()
    {
        $user = User::factory()->create()->assignRole('admin');

        $response = $this->actingAs($user, 'api')->getJson(route('users.index'));

        $response->assertOk();

        $this->assertGreaterThan(0, $response->json('meta.total'));

        $response->assertJsonStructure([
            'meta',
            'links',
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'email',
                    'role_id',
                    'printer_id',
                ],
            ],
        ]);
    }
}
