<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SnsTest extends TestCase
{
    public function testValidation()
    {
        $controller = new \App\Http\Controllers\productController();

        $response = $controller->validation();

        $this->assertTrue($response);
    }
}
