<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CompanyRoutesTest extends TestCase
{
    public function test_if_post_saves_api2cart_key()
    {
        $data = [
            "web_store_key"
        ];

        $response = $this->postJson('api/company/configuration');

        $response->assertOk();
    }
}
