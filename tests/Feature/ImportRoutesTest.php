<?php

namespace Tests\Feature;

use App\User;
use Laravel\Passport\Passport;
use Mockery\Generator\StringManipulation\Pass\Pass;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ImportRoutesTest extends TestCase
{
    public function test_if_import_from_api2cart_route_works()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        $response = $this->get('api/import/orders/from/api2cart');

        $response->assertOk();
    }
}
