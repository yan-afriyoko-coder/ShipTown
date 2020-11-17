<?php

namespace Tests\Feature\Routes\Product;

use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;

class ProductAliases extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        $response = $this->get('api/product/aliases');

        $response->assertStatus(200);
        $this->assertTrue(false);
    }
}
