<?php

namespace Tests\Feature\Http\Controllers\Api\WarehouseController;

use App\Models\Warehouse;
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
        /** @var Warehouse $warehouse */
        $warehouse = factory(Warehouse::class)->make();

        $data = [
            'name'  => $warehouse->name,
            'code'  => $warehouse->code
        ];

        $response = $this->post(route('warehouses.index'), $data);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'id', 'name', 'code',
            ],
        ]);
    }
}
