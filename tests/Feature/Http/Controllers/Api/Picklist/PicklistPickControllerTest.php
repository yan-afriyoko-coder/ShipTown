<?php

namespace Tests\Feature\Http\Controllers\Api\Picklist;

use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Pick;
use App\Models\Product;
use App\User;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Api\Picklist\PicklistPickController
 */
class PicklistPickControllerTest extends TestCase
{
    /**
     * @test
     */
    public function store_returns_an_ok_response()
    {
        $user = factory(User::class)->create();
        $order = factory(Order::class)->create();
        $orderProduct = factory(OrderProduct::class)->create(['order_id' => $order->id]);

        $response = $this->actingAs($user, 'api')->postJson(route('picks.store'), [
            'product_id' => $orderProduct->product_id,
            'quantity_picked' => 1,
            'quantity_skipped_picking' => 0,
            'order_product_ids' => [$orderProduct->id],
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            'data' => [
                // displays array of picked order products
                '*' => [],
            ],
        ]);
    }
}
