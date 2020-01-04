<?php

namespace Tests\Feature;

use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostInventoryUpdateTest extends TestCase
{

    public function test_if_post_is_succesfull() {

        // assign
        Passport::actingAs(
            factory(User::class)->create()
        );

        // act
        $response = $this->post('api/inventory');

        // assert
        $response->assertStatus(200);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_if_route_is_protected()
    {
        $response = $this->post('api/inventory');

        $response->assertStatus(302);
    }
}
