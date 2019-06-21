<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SnsControllerTest extends TestCase
{
    public function test_if_empty_message_is_not_allowed () {

        $data = "";

        $response = $this->withHeaders([
            'Authorization'=>env('TEST_AUTH'),
            ])->json('POST', 'api/products',[
                $data
            ]);

        $response->assertStatus(422);
    }
}
