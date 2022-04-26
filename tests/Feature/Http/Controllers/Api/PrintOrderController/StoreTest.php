<?php

namespace Tests\Feature\Http\Controllers\Api\PrintOrderController;

use App\Models\Order;
use App\User;
use Tests\TestCase;

class StoreTest extends TestCase
{
    /** @test */
    public function test_store_call_returns_ok()
    {
        $this->markTestIncomplete();
//        /** @var Order $order */
//        $order = factory(Order::class)->create();
//        $orderNumber = $order->order_number;
//
//        $user = factory(User::class)->create()->assignRole('admin');
//
//        $response = $this->actingAs($user, 'api')->put("api/print/order/$orderNumber/address_label");
//        $response->assertStatus(200);
//        $response->assertJsonStructure([
//            'data' => [
//                'title',
//                'content_type',
//                'expire_after',
//                'printer_id',
//                'content'
//            ]
//        ]);
    }
}
