<?php

namespace Tests\Feature\routes\api\user\configuration;

use Tests\Feature\AuthorizedUserTestCase;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class getTest extends TestCase
{
    use AuthorizedUserTestCase;

    public function test_successful_get()
    {
        $response = $this->json('GET', 'api/user/configuration');

        $response->assertStatus(200);
    }
}
