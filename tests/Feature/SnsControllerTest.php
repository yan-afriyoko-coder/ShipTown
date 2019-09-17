<?php

namespace Tests\Feature;

use App\Http\Controllers\SnsController;
use App\User;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SnsControllerTest extends TestCase
{
    public function test_if_aws_credentials_are_set()
    {
        $this->assertNotEmpty(env('AWS_ACCESS_KEY_ID'), 'AWS_ACCESS_KEY_ID is not set');
        $this->assertNotEmpty(env('AWS_SECRET_ACCESS_KEY'), 'AWS_SECRET_ACCESS_KEY is not set');
        $this->assertNotEmpty(env('AWS_REGION'), 'AWS_REGION is not set');
        $this->assertNotEmpty(env('AWS_USER_CODE'), 'AWS_USER_CODE is not set');
    }

    public function test_topic_create_subscribe_delete()
    {
        Passport::actingAs(
            factory(User::class)->create()
        );

        $snsClient = new SnsController();

        $this->assertTrue(
            $snsClient->create_user_topic('testPrefix'),
            "Could not create topic"
        );

        $this->assertTrue(
            $snsClient->subscribe_to_user_topic('testPrefix', 'https://blue.black'),
            "Could not subscribe to topic"
        );

        $this->assertTrue(
            $snsClient->delete_user_topic('testPrefix'),
            "Could not delete topic"
        );

    }
}
