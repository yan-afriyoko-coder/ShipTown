<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CompanyRoutesTest extends TestCase
{
    public function test_if_post_route_requires_authentication()
    {
        $data = [
            "web_store_key"
        ];

        $response = $this->postJson('api/company/configuration', $data);

        $response->assertUnauthorized();
    }
}
