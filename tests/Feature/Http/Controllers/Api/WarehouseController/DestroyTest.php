<?php

namespace Tests\Feature\Http\Controllers\Api\WarehouseController;

use App\Models\Warehouse;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $admin = User::factory()->create()->assignRole('admin');
        $this->actingAs($admin, 'api');
    }

    /** @test */
    public function test_destroy_call_returns_ok()
    {
        $warehouse = Warehouse::factory()->create();

        $response = $this->delete(route('warehouses.destroy', $warehouse));
        $response->assertSuccessful();

        $this->assertNull(Warehouse::find($warehouse->id));
    }
}
