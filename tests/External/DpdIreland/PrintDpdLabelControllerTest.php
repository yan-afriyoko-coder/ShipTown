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
    use SeedConfiguration;

    /**
     * @test
     */
    public function storeReturnsOkResponse()
    {
        $user = factory(User::class)->create([
            'address_label_template' => 'dpd_label',
        ]);

        $address = factory(OrderAddress::class)->create([
            'company'      => 'TEST COMPANY',
            'address1'     => 'ATHLONE BUISNESS PARK',
            'address2'     => 'DUBLIN ROAD',
            'phone'        => '12345678901',
            'city'         => 'Dublin',
            'state_name'   => 'Co. Dublin',
            'postcode'     => 'N37KD81',
            'country_code' => 'IRL',
            'country_name' => 'Ireland',
        ]);

        $order = factory(Order::class)->create([
            'shipping_address_id' => $address->getKey(),
        ]);

        $response = $this->actingAs($user, 'api')->putJson('api/print/order/'.$order->order_number.'/dpd_label', [
            // TODO: send request data
        ]);

        $this->assertTrue($response->getStatusCode() === 200, $response->json('message') ?? '');

        $response->assertJsonStructure([
            // TODO: compare expected response data
        ]);

        // TODO: perform additional assertions
    }

    // test cases...
}
