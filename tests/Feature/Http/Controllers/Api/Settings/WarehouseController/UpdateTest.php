<?php

namespace Tests\Feature\Http\Controllers\Api\Settings\WarehouseController;

use App\Models\Warehouse;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $admin = factory(User::class)->create()->assignRole('admin');
        $this->actingAs($admin, 'api');
    }

    /** @test */
    public function test_update_call_returns_ok()
    {
        $wareshouse = factory(Warehouse::class)->create();

        $data = [
            'name'  => 'new name',
            'code'  => 'new code'
        ];

        $response = $this->put(route('api.settings.warehouses.update', $wareshouse), $data);

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'code',
            ],
        ]);

        $updatedWarehouse = Warehouse::find($wareshouse->id);
        $this->assertEquals($updatedWarehouse->name, 'new name');
        $this->assertEquals($updatedWarehouse->code, 'new code');
    }
}
