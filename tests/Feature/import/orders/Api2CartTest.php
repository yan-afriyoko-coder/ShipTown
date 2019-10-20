<?php

namespace Tests\Feature\import\orders;

use App\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Laravel\Passport\Passport;
use League\OAuth2\Server\Entities\Traits\AuthCodeTrait;
use Tests\Feature\AuthorizedUserTestCase;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class api2CartTest extends TestCase
{
    use AuthorizedUserTestCase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_get_route()
    {
        $response = $this->get('api/import/orders/api2cart');

        $response->assertStatus(200);
    }
}
