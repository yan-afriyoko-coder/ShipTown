<?php

namespace Tests\Feature\Http\Controllers\Api\PicklistController;

use App\Models\OrderProduct;
use App\User;
use Tests\TestCase;

class IndexTest extends TestCase
{
    /** @test */
    public function test_index_call_returns_ok()
    {
        OrderProduct::query()->forceDelete();
        factory(OrderProduct::class)->create();

        $user = factory(User::class)->create();

        $response = $this->actingAs($user, 'api')->getJson(route('picklist.index'));

        $response->assertOk();

        $this->assertNotEquals(0, $response->json('meta.total'));

        $response->assertJsonStructure([
            'meta',
            'links',
            'data' => [
                '*' => [
                    'product_id',
                    'name_ordered',
                    'sku_ordered',
                    'total_quantity_to_pick',
                    'inventory_source_shelf_location',
                    'inventory_source_quantity',
                    'quantity_required',
                    'order_product_ids' => [],
                ],
            ],
        ]);
    }
}
