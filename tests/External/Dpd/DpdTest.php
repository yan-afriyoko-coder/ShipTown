<?php

namespace Tests\External\Dpd;

use App\Modules\Dpd\src\Dpd;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DpdTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function if_authenticates()
    {
        $auth = Dpd::getCachedAuthorization();
        $this->assertEquals('OK', $auth['authorization_response']['Status']);
    }

    /**
     * @test
     */
    public function if_authorization_is_cached()
    {
        $auth1 = Dpd::getCachedAuthorization();
        $auth2 = Dpd::getCachedAuthorization();

        $this->assertTrue($auth2['from_cache']);
        $this->assertEquals($auth1['authorization_response']['AccessToken'], $auth2['authorization_response']['AccessToken']);
    }

    /**
     * @test
     */
    public function successfully_generate_preadvice()
    {
        $preAdvice = Dpd::getPreAdvice();

        $this->assertEquals('OK', $preAdvice->status());
    }
}
