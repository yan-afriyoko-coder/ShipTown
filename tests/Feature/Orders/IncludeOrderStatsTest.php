<?php

namespace Tests\Feature\Orders;

use App\Models\Order;
use App\Models\OrderAddress;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Passport\Passport;
use Tests\TestCase;

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
            'filter[order_number]='.$order->order_number.
            '&include=stats',
            []
        );

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'stats',
                ],
            ],
        ]);
    }
}
