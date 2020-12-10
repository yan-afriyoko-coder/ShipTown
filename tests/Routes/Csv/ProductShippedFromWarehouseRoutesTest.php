<?php

namespace Tests\Routes\Csv;

use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;

class ProductShippedFromWarehouseRoutesTest extends TestCase
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

        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
