<?php

namespace Tests\External\DpdIreland;

use App\Models\Order;
use App\Models\OrderAddress;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Api\PrintOrderController
 */
class PrintDpdLabelControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function store_returns_an_ok_response()
    {
        $user = factory(User::class)->create([
            'address_label_template' => 'dpd_label'
        ]);

        $address = factory(OrderAddress::class)->create([
            'company' => 'test',
            'address1' => 'test',
            'address2' => 'test',
            'phone' => '12345678901',
            'city' => 'Dublin',
            'state_name' => 'Co. Dublin',
            'postcode' => 'ABC1234',
            'country_code' => 'IRL'
        ]);

        $order = factory(Order::class)->create([
            'shipping_address_id' => $address->getKey()
        ]);

        $response = $this->actingAs($user, 'api')->putJson('api/print/order/'. $order->order_number .'/dpd_label', [
            // TODO: send request data
        ]);

        $response->assertOk();
        $response->assertJsonStructure([
            // TODO: compare expected response data
        ]);

        // TODO: perform additional assertions
    }

    // test cases...
}
