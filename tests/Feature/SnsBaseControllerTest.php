<?php

namespace Tests\Feature;

use Tests\TestCase;
use Laravel\Passport\Passport;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SnsBaseControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_if_empty_message_is_not_allowed () {

        $data = "";

        Passport::actingAs(
            factory(User::class)->create()
        );


        $this->json('POST', 'api/products', [$data])->assertStatus(422);

    }
}
