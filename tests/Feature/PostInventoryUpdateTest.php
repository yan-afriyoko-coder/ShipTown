<?php

namespace Tests\Feature;

use App\Models\Inventory;
use App\Models\Product;
use App\User;
use Faker\Generator;
use Illuminate\Support\Facades\Event;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostInventoryUpdateTest extends TestCase
{

    public function test_if_post_is_succesfull() {

        // assign
        Event::fake();

        Passport::actingAs(
            factory(User::class)->create()
        );

        $product = factory(Product::class)->create();

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
