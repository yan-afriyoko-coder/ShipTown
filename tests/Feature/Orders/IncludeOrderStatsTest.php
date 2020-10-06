<?php

namespace Tests\Feature\Orders;

use App\Models\Order;
use App\Models\OrderAddress;
use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IncludeOrderStatsTest extends TestCase
{
    /**
     * @return void
     */
    public function testIfCanIncludeStats()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        $order = factory(Order::class)->create();

        $response = $this->get(
            '/api/orders?'.
            "filter[order_number]=".$order->order_number.
            "&include=stats",
            []
        );

        $this->assertEquals(1, $response->json('total'));

        $response->assertJsonStructure([
            "data" => [
                '*' => [
                    'stats'
                ]
            ]
        ]);
    }
}
