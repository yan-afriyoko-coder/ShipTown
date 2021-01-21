<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Models\Order;
use App\Models\OrderProduct;
use App\User;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Api\PicklistController
 */
class PicklistControllerTest extends TestCase
{
    /**
     * @test
     */
    public function index_returns_an_ok_response()
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
                    'order_product_ids' => []
                ]
            ]
        ]);
    }

    // test cases...
}
