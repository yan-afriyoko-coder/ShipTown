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
        /** @var Warehouse $warehouse */
        $warehouse = factory(Warehouse::class)->create();

        /** @var Warehouse $newWarehouse */
        $newWarehouse = factory(Warehouse::class)->make();

        $data = [
            'name'  => $newWarehouse->name,
            'code'  => $newWarehouse->code
        ];

        $response = $this->put(route('api.settings.warehouses.update', $warehouse), $data);

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'code',
            ],
        ]);

        $updatedWarehouse = Warehouse::find($warehouse->id);
        $this->assertEquals($updatedWarehouse->name, $newWarehouse->name);
        $this->assertEquals($updatedWarehouse->code, $newWarehouse->code);
    }
}
