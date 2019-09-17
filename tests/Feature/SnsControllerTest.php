<?php

namespace Tests\Feature;

use App\Http\Controllers\SnsBaseController;
use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SnsControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_if_aws_credentials_are_set()
    {
        $this->assertNotEmpty(env('AWS_ACCESS_KEY_ID'), 'AWS_ACCESS_KEY_ID is not set');
        $this->assertNotEmpty(env('AWS_SECRET_ACCESS_KEY'), 'AWS_SECRET_ACCESS_KEY is not set');
        $this->assertNotEmpty(env('AWS_REGION'), 'AWS_REGION is not set');
        $this->assertNotEmpty(env('AWS_USER_CODE'), 'AWS_USER_CODE is not set');
    }

    public function test_topic_creation()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        $snsClient = new SnsBaseController();

        $result = $snsClient->createTopic('test');

        $this->assertTrue($result);
    }
}
