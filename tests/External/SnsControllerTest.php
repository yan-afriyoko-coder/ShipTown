<?php

namespace Tests\External;

use App\Modules\Webhooks\src\Services\SnsService;
use App\User;
use Tests\TestCase;

class SnsControllerTest extends TestCase
{
    public function testIfAwsCredentialsAreSet()
    {
        $this->assertNotEmpty(env('AWS_ACCESS_KEY_ID'), 'AWS_ACCESS_KEY_ID is not set');
        $this->assertNotEmpty(env('AWS_SECRET_ACCESS_KEY'), 'AWS_SECRET_ACCESS_KEY is not set');
        $this->assertNotEmpty(env('AWS_REGION'), 'AWS_REGION is not set');
        $this->assertNotEmpty(env('AWS_USER_CODE'), 'AWS_USER_CODE is not set');
    }

    public function testTopicCreateSubscribeDelete()
    {
        $this->actingAs(
            factory(User::class)->create()
        );

        $snsClient = new SnsService('testTopic');

        $this->assertTrue(
            $snsClient->createTopic(),
            'Could not create topic'
        );

        $this->assertTrue(
            $snsClient->subscribeToTopic('https://demo.products.management'),
            'Could not subscribe to topic'
        );

        $this->assertTrue(
            $snsClient->publish('This is test message'),
            'Could not publish message'
        );

        $this->assertTrue(
            $snsClient->deleteTopic(),
            'Could not delete topic'
        );
    }
}
