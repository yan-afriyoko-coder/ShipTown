<?php

namespace Tests\Feature\Http\Controllers\Api\Settings\WarehouseController;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $admin = factory(User::class)->create()->assignRole('admin');
        $this->actingAs($admin, 'api');
    }

    /** @test */
    public function test_store_call_returns_ok()
    {
        $data = [
            'name'  => 'Some warehouse',
            'code'  => 'mywarehouse'
        ];

        $response = $this->post(route('api.settings.warehouses.index'), $data);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'id', 'name', 'code',
            ],
        ]);
    }
}
