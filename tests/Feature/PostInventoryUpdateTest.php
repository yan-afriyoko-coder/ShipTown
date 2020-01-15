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
