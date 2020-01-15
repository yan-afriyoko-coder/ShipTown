<?php

namespace Tests\Feature\api\inventory;

use App\User;
use Laravel\Passport\Passport;
use Mockery\Generator\StringManipulation\Pass\Pass;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InventoryPostTest extends TestCase
{

    public function test_if_cant_post_without_data()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        $response = $this->postJson('/api/inventory', []);

        $response->assertStatus(422);
    }
}
