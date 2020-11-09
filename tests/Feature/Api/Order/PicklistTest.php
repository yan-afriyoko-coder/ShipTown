<?php

namespace Tests\Feature\Api\Order;

use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;

class PicklistTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testIndex()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        $response = $this->get('/api/order/picklist');

        $response->assertStatus(200);
    }
}
