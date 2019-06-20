<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductControllerTest extends TestCase
{
    public function testResponseStatus()
    {
        $response = $this->withHeaders([
            'Authorization'=>env('TEST_AUTH'),
        ])->json('POST', 'api/products', ['Message' => 'test']);

        $response ->assertStatus(200);
    }

    public function testValidation()
    {
        $controller = new \App\Http\Controllers\productController();

        $validMessage = "Hello";

        $response = $controller->validation($validMessage);

        $this->assertTrue($response);

        $invalidMessage = null;

        $response = $controller->validation($invalidMessage);

        $this->assertFalse($response);
    }
}
