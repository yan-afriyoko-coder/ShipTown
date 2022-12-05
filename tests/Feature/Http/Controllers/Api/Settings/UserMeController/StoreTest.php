<?php

namespace Tests\Feature\Http\Controllers\Api\Settings\UserMeController;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $admin = User::factory()->create()->assignRole('admin');
        $this->actingAs($admin, 'api');
    }

    /** @test */
    public function test_store_call_returns_ok()
    {
        $params = [
            'name'                    => 'User Test',
            'printer_id'              => 1,
            'ask_for_shipping_number' => false,
        ];

        $response = $this->post(route('me.store', $params));
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'email',
                'location_id',
                'role_id',
                'role_name',
                'printer_id',
                'address_label_template'
            ],
        ]);
    }
}
