<?php

namespace Tests\Feature\Http\Controllers\Api\PacklistOrderController;

use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Warehouse;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_index_call_returns_ok()
    {
        factory(OrderStatus::class)->create([
            'code' => 'packing',
            'name' => 'packing',
            'order_active' => true,
            'order_on_hold' => false,
        ]);

        $warehouse = factory(Warehouse::class)->create();

        factory(Order::class)->create(['status_code' => 'packing']);

        /** @var User $user */
        $user = factory(User::class)->create([
            'location_id' => $warehouse->getKey()
        ]);

        $response = $this->actingAs($user, 'api')
            ->getJson(route('packlist.order.index', [
                'filter[inventory_source_warehouse_id]' => $user->location_id
            ]));

        $response->assertOk();

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                ],
            ],
        ]);
    }

    /** @test */
    public function test_index_call_returns_422()
    {
        $user = factory(User::class)->create();

        factory(OrderStatus::class)->create([
            'code' => 'packing',
            'name' => 'packing',
            'order_active' => true,
            'order_on_hold' => false,
        ]);

        factory(Order::class)->create(['status_code' => 'packing']);

        $response = $this->actingAs($user, 'api')->getJson(route('packlist.order.index'));

        $response->assertStatus(422);

        $response->assertJsonStructure([
            'errors' => [
                'filter' => [],
                'filter.inventory_source_warehouse_id' => []
            ]
        ]);
    }
}
